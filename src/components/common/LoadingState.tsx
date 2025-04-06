
import React from "react";

interface LoadingStateProps {
  message?: string;
}

const LoadingState: React.FC<LoadingStateProps> = ({ 
  message = "Chargement des données..." 
}) => {
  return (
    <div className="text-center py-8">
      <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900 mb-2"></div>
      <p>{message}</p>
    </div>
  );
};

export default LoadingState;
