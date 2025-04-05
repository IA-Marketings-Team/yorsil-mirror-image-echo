
import React from "react";

interface StatusBadgeProps {
  status: "success" | "pending" | "error" | "warning" | "info";
  text?: string;
}

const StatusBadge: React.FC<StatusBadgeProps> = ({ 
  status, 
  text = status === "success" ? "Validé" : 
         status === "pending" ? "En attente" : 
         status === "error" ? "Erreur" : 
         status === "warning" ? "Attention" : "Info"
}) => {
  const bgColor = 
    status === "success" ? "bg-green-100 text-green-800" : 
    status === "pending" ? "bg-yellow-100 text-yellow-800" : 
    status === "error" ? "bg-red-100 text-red-800" : 
    status === "warning" ? "bg-orange-100 text-orange-800" :
    "bg-blue-100 text-blue-800";

  return (
    <span className={`px-3 py-1 rounded-full text-xs font-medium ${bgColor}`}>
      {text}
    </span>
  );
};

export default StatusBadge;
