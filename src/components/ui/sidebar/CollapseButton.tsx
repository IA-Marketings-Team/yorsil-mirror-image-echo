
import React from "react";
import { Button } from "@/components/ui/button";
import { ChevronLeft, ChevronRight } from "lucide-react";

interface CollapseButtonProps {
  collapsed: boolean;
  onClick: () => void;
}

export const CollapseButton: React.FC<CollapseButtonProps> = ({
  collapsed,
  onClick
}) => {
  return (
    <Button 
      variant="ghost" 
      size="default"
      className="h-8 w-8 p-0"
      onClick={onClick}
    >
      {collapsed ? (
        <ChevronRight className="h-4 w-4" />
      ) : (
        <ChevronLeft className="h-4 w-4" />
      )}
    </Button>
  );
};
