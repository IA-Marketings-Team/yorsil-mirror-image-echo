
import { useState } from "react";

export interface UseSidebarItemProps {
  hasChildren: boolean;
  defaultOpen?: boolean;
  onClick?: () => void;
}

export function useSidebarItem({ hasChildren, defaultOpen = false, onClick }: UseSidebarItemProps) {
  const [open, setOpen] = useState(defaultOpen);
  
  const handleClick = (e: React.MouseEvent) => {
    if (hasChildren) {
      e.preventDefault();
      setOpen(!open);
    }
    
    if (onClick) {
      onClick();
    }
  };

  return {
    open,
    setOpen,
    handleClick,
  };
}
