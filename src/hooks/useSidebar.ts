
import { useState } from "react";

export interface UseSidebarProps {
  defaultCollapsed?: boolean;
}

export function useSidebar({ defaultCollapsed = false }: UseSidebarProps = {}) {
  const [collapsed, setCollapsed] = useState(defaultCollapsed);

  const toggleCollapsed = () => setCollapsed(!collapsed);

  return {
    collapsed,
    toggleCollapsed
  };
}
