
import { cn } from "@/lib/utils";

interface StatsCardProps {
  title: string;
  value: React.ReactNode;
  description?: string;
  icon?: React.ReactNode;
  iconColor?: string;
  iconBackground?: string;
  trend?: number;
  className?: string;
}

const StatsCard = ({
  title,
  value,
  description,
  icon,
  iconColor = "text-blue-500",
  iconBackground = "bg-blue-100",
  trend,
  className,
}: StatsCardProps) => {
  return (
    <div className={cn("bg-white rounded-lg shadow-sm p-6", className)}>
      <div className="flex items-start">
        {icon && (
          <div className={cn("rounded-full p-3 mr-4", iconBackground)}>
            <span className={iconColor}>{icon}</span>
          </div>
        )}
        <div>
          <h3 className="font-medium text-gray-500">{title}</h3>
          <div className="mt-1 flex items-baseline">
            <p className="text-2xl font-semibold text-gray-900">{value}</p>
            {trend !== undefined && (
              <p
                className={cn(
                  "ml-2 flex items-baseline text-sm",
                  trend >= 0 ? "text-green-600" : "text-red-600"
                )}
              >
                {trend >= 0 ? "+" : ""}
                {trend}%
              </p>
            )}
          </div>
          {description && (
            <p className="mt-1 text-sm text-gray-500">{description}</p>
          )}
        </div>
      </div>
    </div>
  );
};

export default StatsCard;
