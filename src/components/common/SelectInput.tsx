
import React, { useState } from "react";
import { ChevronDown, ChevronUp } from "lucide-react";

interface Option {
  label: string;
  value: string | number;
}

interface SelectInputProps {
  options: Option[];
  placeholder?: string;
  value?: string | number;
  onChange?: (value: string | number) => void;
  className?: string;
  label?: string;
}

const SelectInput: React.FC<SelectInputProps> = ({
  options,
  placeholder = "Sélectionner une option",
  value,
  onChange,
  className = "",
  label
}) => {
  const [isOpen, setIsOpen] = useState(false);
  const [selectedOption, setSelectedOption] = useState<Option | null>(
    value ? options.find(opt => opt.value === value) || null : null
  );

  const handleSelect = (option: Option) => {
    setSelectedOption(option);
    setIsOpen(false);
    if (onChange) onChange(option.value);
  };

  return (
    <div className="relative w-full">
      {label && <label className="block text-gray-700 mb-1">{label}</label>}
      <button
        type="button"
        className={`w-full flex items-center justify-between bg-white border border-gray-300 rounded-md px-4 py-2 text-left focus:outline-none ${className}`}
        onClick={() => setIsOpen(!isOpen)}
      >
        <span className={selectedOption ? "text-gray-700" : "text-gray-500"}>
          {selectedOption ? selectedOption.label : placeholder}
        </span>
        {isOpen ? <ChevronUp size={16} /> : <ChevronDown size={16} />}
      </button>

      {isOpen && (
        <div className="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
          <ul>
            {options.map((option) => (
              <li
                key={option.value}
                className={`px-4 py-2 hover:bg-blue-100 cursor-pointer ${
                  selectedOption?.value === option.value ? "bg-blue-500 text-white" : ""
                }`}
                onClick={() => handleSelect(option)}
              >
                {option.label}
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
};

export default SelectInput;
