
import { useState } from "react";

export interface UseSidebarGroupProps {
  defaultOpen?: boolean;
}

export function useSidebarGroup({ defaultOpen = true }: UseSidebarGroupProps = {}) {
  const [open, setOpen] = useState(defaultOpen);

  const toggleOpen = () => setOpen(!open);

  return {
    open,
    toggleOpen
  };
}
