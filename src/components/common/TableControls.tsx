
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
      <div className="flex justify-between items-center">
        <div className="flex items-center">
          <span className="mr-2">Afficher</span>
          <select
            className="border rounded-md px-2 py-1"
            defaultValue={elementsPerPage}
          >
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          <span className="ml-2">éléments</span>
        </div>

        {onSearch && (
          <div className="flex items-center">
            <span className="mr-2">Rechercher:</span>
            <Input
              type="text"
              className="w-64"
              onChange={(e) => onSearch(e.target.value)}
            />
          </div>
        )}
      </div>

      {totalPages > 1 && (
        <div className="flex justify-end mt-4">
          <div className="flex items-center">
            <span className="mr-2">
              Affichage d'éléments {Math.min((currentPage - 1) * elementsPerPage + 1, totalElements)} à{" "}
              {Math.min(currentPage * elementsPerPage, totalElements)} sur {totalElements} éléments
            </span>
            
            <div className="flex">
              <button
                onClick={() => onPageChange(currentPage - 1)}
                disabled={currentPage === 1}
                className="border border-r-0 rounded-l-md px-2 py-1 disabled:opacity-50"
              >
                <ChevronLeft size={18} />
              </button>
              
              {Array.from({ length: Math.min(totalPages, 5) }).map((_, i) => (
                <button
                  key={i}
                  onClick={() => onPageChange(i + 1)}
                  className={`border border-l-0 px-3 py-1 ${
                    currentPage === i + 1 ? "bg-blue-500 text-white" : ""
                  }`}
                >
                  {i + 1}
                </button>
              ))}
              
              <button
                onClick={() => onPageChange(currentPage + 1)}
                disabled={currentPage === totalPages}
                className="border border-l-0 rounded-r-md px-2 py-1 disabled:opacity-50"
              >
                <ChevronRight size={18} />
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default TableControls;
