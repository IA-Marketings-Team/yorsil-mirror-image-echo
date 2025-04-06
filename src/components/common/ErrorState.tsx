
import React from "react";

interface ErrorStateProps {
  error: Error | string;
  message?: string;
}

const ErrorState: React.FC<ErrorStateProps> = ({ 
  error, 
  message = "Une erreur est survenue" 
}) => {
  const errorMessage = typeof error === "string" ? error : error.message;

  return (
    <div className="text-center py-8 text-red-500">
      <p className="font-semibold mb-2">{message}</p>
      <p className="text-sm">{errorMessage}</p>
    </div>
  );
};

export default ErrorState;
