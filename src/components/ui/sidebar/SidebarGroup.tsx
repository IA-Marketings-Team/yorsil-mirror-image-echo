
import React from "react";
import { cn } from "@/lib/utils";
import { ChevronDown } from "lucide-react";
import { Button } from "@/components/ui/button";
import { SidebarGroupProps } from "@/types/sidebar.types";
import { useSidebarGroup } from "@/hooks/useSidebarGroup";

const SidebarGroup: React.FC<SidebarGroupProps> = ({
  title,
  collapsible = true,
  defaultOpen = true,
  children,
}) => {
  const { open, toggleOpen } = useSidebarGroup({ defaultOpen });

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
              onClick={toggleOpen} 
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

export { SidebarGroup };
