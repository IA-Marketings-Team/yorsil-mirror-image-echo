
import React from "react";

interface BusinessCardProps {
  title: string;
  value: string;
  width?: string;
}

const BusinessCard: React.FC<BusinessCardProps> = ({ 
  title, 
  value,
  width = "w-full" 
}) => {
  return (
    <div className={`${width} p-6 rounded-lg bg-yorsil-blue`}>
      <h3 className="text-center text-gray-600 text-sm">{title}</h3>
      <p className="text-center text-xl font-bold mt-2 text-yorsil-text">{value}</p>
    </div>
  );
};

export default BusinessCard;
