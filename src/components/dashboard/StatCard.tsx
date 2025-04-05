
import React from "react";

interface StatCardProps {
  title: string;
  value: string;
  backgroundColor: string;
  textColor: string;
  width?: string;
}

const StatCard: React.FC<StatCardProps> = ({ 
  title, 
  value, 
  backgroundColor, 
  textColor,
  width = "w-full"
}) => {
  return (
    <div className={`${width} p-6 rounded-lg ${backgroundColor}`}>
      <h2 className="text-lg font-semibold uppercase text-center">{title}</h2>
      <p className={`text-center text-3xl font-bold mt-2 ${textColor}`}>{value}</p>
    </div>
  );
};

export default StatCard;
