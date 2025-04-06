
import React from "react";
import { cn } from "@/lib/utils";
import { ChevronDown, ExternalLink } from "lucide-react";
import { SidebarItemProps } from "@/types/sidebar.types";
import { useSidebarItem } from "@/hooks/useSidebarItem";

const SidebarItem = React.forwardRef<HTMLAnchorElement, SidebarItemProps>(
  ({ icon, title, href, active, disabled, external, children, onClick, variant = "ghost" }, ref) => {
    const hasChildren = React.Children.count(children) > 0;
    const { open, handleClick } = useSidebarItem({ hasChildren, onClick });

    const commonClasses = cn(
      "flex items-center py-2 px-3 text-sm rounded-md w-full",
      active && "bg-accent text-accent-foreground font-medium",
      disabled && "opacity-50 cursor-not-allowed pointer-events-none",
      hasChildren && "justify-between",
      variant === "default" && "bg-primary text-primary-foreground hover:bg-primary/90",
      variant === "ghost" && "hover:bg-accent hover:text-accent-foreground",
      variant === "outline" && "border border-input hover:bg-accent hover:text-accent-foreground"
    );

    const renderContent = () => (
      <>
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
      </>
    );

    if (href) {
      return (
        <div className="w-full">
          <a
            ref={ref}
            href={href}
            target={external ? "_blank" : undefined}
            rel={external ? "noreferrer" : undefined}
            onClick={handleClick}
            className={commonClasses}
          >
            {renderContent()}
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
          onClick={handleClick as any}
          disabled={disabled}
          className={commonClasses}
        >
          {renderContent()}
        </button>
        {hasChildren && open && (
          <div className="ml-4 pl-2 border-l mt-1">{children}</div>
        )}
      </div>
    );
  }
);

SidebarItem.displayName = "SidebarItem";

export { SidebarItem };
