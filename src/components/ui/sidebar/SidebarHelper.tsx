
import React, { ReactElement, isValidElement, ReactNode } from "react";
import { SidebarItem } from "./SidebarItem";
import { SidebarGroup } from "./SidebarGroup";
import { SidebarItemProps } from "@/types/sidebar.types";

export function renderCollapsedItems(children: ReactNode): ReactNode[] | null {
  return React.Children.map(children, (child) => {
    if (!isValidElement(child)) return null;
    
    // Handle SidebarGroup
    if (child.type === SidebarGroup) {
      return React.Children.map(child.props.children, (groupChild) => {
        if (!isValidElement(groupChild)) return null;
        
        // Handle SidebarItem within a group
        if (groupChild.type === SidebarItem) {
          // Use type assertion to inform TypeScript that props is a SidebarItemProps
          const props = groupChild.props as SidebarItemProps;
          const newProps = {
            icon: props.icon,
            variant: props.variant,
            active: props.active,
            disabled: props.disabled,
            href: props.href,
            external: props.external,
            onClick: props.onClick,
            title: "", // Override title to be empty
          };
          return React.cloneElement(groupChild, newProps);
        }
        return null;
      });
    } 
    // Handle direct SidebarItem
    else if (child.type === SidebarItem) {
      // Use type assertion to inform TypeScript that props is a SidebarItemProps
      const props = child.props as SidebarItemProps;
      const newProps = {
        icon: props.icon,
        variant: props.variant,
        active: props.active,
        disabled: props.disabled,
        href: props.href,
        external: props.external,
        onClick: props.onClick,
        title: "", // Override title to be empty
      };
      return React.cloneElement(child, newProps);
    }
    return null;
  });
}
