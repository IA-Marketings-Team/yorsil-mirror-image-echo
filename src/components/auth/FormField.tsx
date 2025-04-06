
import React, { ReactNode } from "react";

interface FormFieldProps {
  id: string;
  label: string;
  children: ReactNode;
  required?: boolean;
  error?: string;
}

const FormField: React.FC<FormFieldProps> = ({
  id,
  label,
  children,
  required = false,
  error,
}) => {
  return (
    <div>
      <label htmlFor={id} className="block text-sm font-medium text-gray-700">
        {label} {required && <span className="text-red-500">*</span>}
      </label>
      <div className="mt-1">
        {children}
        {error && <p className="mt-1 text-xs text-red-500">{error}</p>}
      </div>
    </div>
  );
};

export default FormField;
