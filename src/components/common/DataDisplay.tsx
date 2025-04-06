
import React from 'react';
import { cn } from '@/lib/utils';

interface DataDisplayProps {
  title: string;
  value: React.ReactNode;
  icon?: React.ReactNode;
  trend?: number;
  className?: string;
  trendLabel?: string;
}

const DataDisplay = ({ 
  title, 
  value, 
  icon, 
  trend, 
  className,
  trendLabel 
}: DataDisplayProps) => {
  return (
    <div className={cn("bg-white p-6 rounded-lg shadow-sm", className)}>
      <div className="flex items-center justify-between">
        <span className="text-sm font-medium text-gray-500">{title}</span>
        {icon && <div className="text-blue-500">{icon}</div>}
      </div>
      
      <div className="mt-2">
        <span className="text-2xl font-bold">{value}</span>
      </div>
      
      {trend !== undefined && (
        <div className="mt-2 flex items-center">
          <span
            className={cn(
              "text-xs font-medium",
              trend > 0 ? "text-green-600" : trend < 0 ? "text-red-600" : "text-gray-500"
            )}
          >
            {trend > 0 ? '+' : ''}{trend}% 
          </span>
          {trendLabel && (
            <span className="ml-2 text-xs text-gray-500">{trendLabel}</span>
          )}
        </div>
      )}
    </div>
  );
};

export default DataDisplay;
