
import React from "react";
import { cn } from "@/lib/utils";
import { SidebarProps } from "@/types/sidebar.types";
import { useSidebar } from "@/hooks/useSidebar";
import { CollapseButton } from "./CollapseButton";
import { renderCollapsedItems } from "./SidebarHelper";

const Sidebar: React.FC<SidebarProps> = ({
  className,
  children,
  collapsible = true,
  defaultCollapsed = false,
  headerContent,
  footerContent,
}) => {
  const { collapsed, toggleCollapsed } = useSidebar({ defaultCollapsed });

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
            <CollapseButton collapsed={collapsed} onClick={toggleCollapsed} />
          </div>
        )}
        
        <div className={cn("px-2", collapsed && "items-center")}>
          {collapsed ? (
            <div className="space-y-2 py-2">
              {renderCollapsedItems(children)}
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

export { Sidebar };
