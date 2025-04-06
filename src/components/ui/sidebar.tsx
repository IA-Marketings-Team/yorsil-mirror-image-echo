
import React, { useState } from "react";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { ChevronLeft, ChevronRight, ChevronDown, ExternalLink } from "lucide-react";

interface SidebarItemProps {
  icon?: React.ReactNode;
  title: string;
  href?: string;
  active?: boolean;
  disabled?: boolean;
  external?: boolean;
  children?: React.ReactNode;
  onClick?: () => void;
  variant?: "default" | "ghost" | "outline";
}

interface SidebarGroupProps {
  title?: string;
  collapsible?: boolean;
  defaultOpen?: boolean;
  children: React.ReactNode;
}

interface SidebarProps {
  className?: string;
  children: React.ReactNode;
  collapsible?: boolean;
  defaultCollapsed?: boolean;
  headerContent?: React.ReactNode;
  footerContent?: React.ReactNode;
}

const SidebarItem = React.forwardRef<HTMLAnchorElement, SidebarItemProps>(
  ({ icon, title, href, active, disabled, external, children, onClick, variant = "ghost" }, ref) => {
    const [open, setOpen] = useState(false);
    const hasChildren = React.Children.count(children) > 0;

    const handleClick = (e: React.MouseEvent) => {
      if (hasChildren) {
        e.preventDefault();
        setOpen(!open);
      }
      
      if (onClick) {
        onClick();
      }
    };

    if (href) {
      return (
        <div className="w-full">
          <a
            ref={ref}
            href={href}
            target={external ? "_blank" : undefined}
            rel={external ? "noreferrer" : undefined}
            onClick={handleClick}
            className={cn(
              "flex items-center py-2 px-3 text-sm rounded-md w-full",
              active && "bg-accent text-accent-foreground font-medium",
              disabled && "opacity-50 cursor-not-allowed pointer-events-none",
              hasChildren && "justify-between",
              variant === "default" && "bg-primary text-primary-foreground hover:bg-primary/90",
              variant === "ghost" && "hover:bg-accent hover:text-accent-foreground",
              variant === "outline" && "border border-input hover:bg-accent hover:text-accent-foreground"
            )}
          >
            {icon && <span className="mr-2">{icon}</span>}
            <span className="flex-grow text-left">{title}</span>
            {hasChildren && (
              <ChevronDown 
                className={cn(
                  "h-4 w-4 transition-transform", 
                  open && "transform rotate-180"
                )} 
              />
            )}
            {external && <ExternalLink className="h-3 w-3 ml-1" />}
          </a>
          {hasChildren && open && (
            <div className="ml-4 pl-2 border-l mt-1">{children}</div>
          )}
        </div>
      );
    }

    return (
      <div className="w-full">
        <button
          onClick={handleClick}
          disabled={disabled}
          className={cn(
            "flex items-center py-2 px-3 text-sm rounded-md w-full",
            active && "bg-accent text-accent-foreground font-medium",
            disabled && "opacity-50 cursor-not-allowed pointer-events-none",
            hasChildren && "justify-between",
            variant === "default" && "bg-primary text-primary-foreground hover:bg-primary/90",
            variant === "ghost" && "hover:bg-accent hover:text-accent-foreground",
            variant === "outline" && "border border-input hover:bg-accent hover:text-accent-foreground"
          )}
        >
          {icon && <span className="mr-2">{icon}</span>}
          <span className="flex-grow text-left">{title}</span>
          {hasChildren && (
            <ChevronDown 
              className={cn(
                "h-4 w-4 transition-transform", 
                open && "transform rotate-180"
              )} 
            />
          )}
        </button>
        {hasChildren && open && (
          <div className="ml-4 pl-2 border-l mt-1">{children}</div>
        )}
      </div>
    );
  }
);

SidebarItem.displayName = "SidebarItem";

const SidebarGroup = ({
  title,
  collapsible = true,
  defaultOpen = true,
  children,
}: SidebarGroupProps) => {
  const [open, setOpen] = useState(defaultOpen);

  return (
    <div className="pb-2">
      {title && (
        <div className="flex items-center justify-between py-2">
          <h3 className="text-xs font-medium text-muted-foreground uppercase tracking-wider px-3">
            {title}
          </h3>
          {collapsible && (
            <Button 
              variant="ghost" 
              size="sm" 
              onClick={() => setOpen(!open)} 
              className="h-6 w-6 p-0"
            >
              <ChevronDown className={cn("h-4 w-4", open ? "transform rotate-180" : "")} />
            </Button>
          )}
        </div>
      )}
      {(!collapsible || open) && <div className="space-y-1">{children}</div>}
    </div>
  );
};

SidebarGroup.displayName = "SidebarGroup";

const Sidebar = ({
  className,
  children,
  collapsible = true,
  defaultCollapsed = false,
  headerContent,
  footerContent,
}: SidebarProps) => {
  const [collapsed, setCollapsed] = useState(defaultCollapsed);

  // Function to handle cloning with proper type safety
  const renderCollapsedItems = () => {
    return React.Children.map(children, (child) => {
      if (!React.isValidElement(child)) return null;
      
      // Handle SidebarGroup
      if (child.type === SidebarGroup) {
        return React.Children.map(child.props.children, (groupChild) => {
          if (!React.isValidElement(groupChild)) return null;
          
          // Handle SidebarItem within a group
          if (groupChild.type === SidebarItem) {
            // Create a new props object without spreading
            const newProps = {
              ...groupChild.props,
              title: "",
              children: null
            };
            return React.cloneElement(groupChild, newProps);
          }
          return null;
        });
      } 
      // Handle direct SidebarItem
      else if (child.type === SidebarItem) {
        // Create a new props object without spreading
        const newProps = {
          ...child.props,
          title: "",
          children: null
        };
        return React.cloneElement(child, newProps);
      }
      return null;
    });
  };

  return (
    <div className={cn("flex flex-col h-full border-r bg-background", className)}>
      {headerContent && (
        <div className="px-4 py-3 border-b">
          {headerContent}
        </div>
      )}
      
      <div className="flex-1 overflow-y-auto">
        {collapsible && (
          <div className="p-2 flex justify-end">
            <Button 
              variant="ghost" 
              size="default"
              className="h-8 w-8 p-0"
              onClick={() => setCollapsed(!collapsed)}
            >
              {collapsed ? (
                <ChevronRight className="h-4 w-4" />
              ) : (
                <ChevronLeft className="h-4 w-4" />
              )}
            </Button>
          </div>
        )}
        
        <div className={cn("px-2", collapsed && "items-center")}>
          {collapsed ? (
            <div className="space-y-2 py-2">
              {renderCollapsedItems()}
            </div>
          ) : (
            <div className="space-y-2 py-2">{children}</div>
          )}
        </div>
      </div>
      
      {footerContent && (
        <div className="px-4 py-3 border-t">
          {footerContent}
        </div>
      )}
    </div>
  );
};

Sidebar.displayName = "Sidebar";

export { Sidebar, SidebarItem, SidebarGroup };
