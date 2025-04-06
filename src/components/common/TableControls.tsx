
import React from "react";
import { Input } from "@/components/ui/input";
import { ChevronLeft, ChevronRight } from "lucide-react";

interface TableControlsProps {
  totalElements: number;
  elementsPerPage: number;
  currentPage: number;
  onPageChange: (page: number) => void;
  onSearch?: (query: string) => void;
}

const TableControls: React.FC<TableControlsProps> = ({
  totalElements,
  elementsPerPage,
  currentPage,
  onPageChange,
  onSearch
}) => {
  const totalPages = Math.ceil(totalElements / elementsPerPage);
  
  return (
    <div className="w-full flex flex-col gap-4">
      <div className="flex justify-between items-center mb-4">
        <div className="flex items-center">
          <span className="mr-2 text-gray-600">Afficher</span>
          <select
            className="border rounded-md px-2 py-1 text-gray-700 bg-white"
            defaultValue={elementsPerPage}
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span className="ml-2 text-gray-600">éléments</span>
        </div>

        {onSearch && (
          <div className="flex items-center">
            <span className="mr-2 text-gray-600">Rechercher:</span>
            <Input
              type="text"
              className="w-64"
              onChange={(e) => onSearch(e.target.value)}
              placeholder="Saisir un terme de recherche..."
            />
          </div>
        )}
      </div>
    </div>
  );
};

export default TableControls;
