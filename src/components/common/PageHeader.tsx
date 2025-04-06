
import React, { ReactNode } from "react";
import { Button } from "@/components/ui/button";

interface PageHeaderProps {
  title: string;
  action?: {
    label: string;
    icon?: React.ReactNode;
    onClick?: () => void;
  };
}

const PageHeader: React.FC<PageHeaderProps> = ({ title, action }) => {
  return (
    <div className="flex justify-between items-center">
      <h1 className="text-2xl font-bold">{title}</h1>
      {action && (
        <Button className="flex items-center gap-2" onClick={action.onClick}>
          {action.icon}
          {action.label}
        </Button>
      )}
    </div>
  );
};

export default PageHeader;
