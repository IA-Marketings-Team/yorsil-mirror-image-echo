
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
    status === "success" ? "bg-green-100 text-green-800 border border-green-200" : 
    status === "pending" ? "bg-yellow-100 text-yellow-800 border border-yellow-200" : 
    status === "error" ? "bg-red-100 text-red-800 border border-red-200" : 
    status === "warning" ? "bg-orange-100 text-orange-800 border border-orange-200" :
    "bg-blue-100 text-blue-800 border border-blue-200";

  return (
    <span className={`inline-block px-3 py-1 rounded-full text-xs font-medium ${bgColor}`}>
      {text}
    </span>
  );
};

export default StatusBadge;
