
import { ReactNode } from "react";
import { VariantProps } from "class-variance-authority";
import { buttonVariants } from "@/components/ui/button";

export interface SidebarItemProps {
  icon?: ReactNode;
  title: string;
  href?: string;
  active?: boolean;
  disabled?: boolean;
  external?: boolean;
  children?: ReactNode;
  onClick?: () => void;
  variant?: "default" | "ghost" | "outline";
}

export interface SidebarGroupProps {
  title?: string;
  collapsible?: boolean;
  defaultOpen?: boolean;
  children: ReactNode;
}

export interface SidebarProps {
  className?: string;
  children: ReactNode;
  collapsible?: boolean;
  defaultCollapsed?: boolean;
  headerContent?: ReactNode;
  footerContent?: ReactNode;
}
