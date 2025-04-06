
import { useState } from "react";
import { ChevronLeft, ChevronRight, Search } from "lucide-react";
import { cn } from "@/lib/utils";

interface Column {
  key: string;
  header: string;
  render?: (value: any, row: any) => React.ReactNode;
  width?: string;
}

interface TableDisplayProps<T> {
  data: T[];
  columns: Column[];
  emptyMessage?: string;
  onRowClick?: (row: T) => void;
  pagination?: boolean;
  searchable?: boolean;
  className?: string;
}

function TableDisplay<T>({
  data,
  columns,
  emptyMessage = "Aucune donnée disponible",
  onRowClick,
  pagination = true,
  searchable = true,
  className,
}: TableDisplayProps<T>) {
  const [currentPage, setCurrentPage] = useState(1);
  const [searchTerm, setSearchTerm] = useState("");
  const itemsPerPage = 10;

  // Filter data by search term if searchable
  const filteredData = searchable
    ? data.filter((item) => {
        const itemValues = Object.values(item as object);
        return itemValues.some((value) => {
          if (typeof value === "string") {
            return value.toLowerCase().includes(searchTerm.toLowerCase());
          }
          return false;
        });
      })
    : data;

  // Calculate pagination
  const totalPages = Math.ceil(filteredData.length / itemsPerPage);
  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const currentData = filteredData.slice(startIndex, endIndex);

  const handlePageChange = (page: number) => {
    setCurrentPage(page);
  };

  const renderPagination = () => {
    if (!pagination || totalPages <= 1) return null;

    return (
      <div className="flex items-center justify-between mt-4 px-4">
        <div className="text-sm text-gray-700">
          Affichage de <span className="font-medium">{startIndex + 1}</span> à{" "}
          <span className="font-medium">
            {Math.min(endIndex, filteredData.length)}
          </span>{" "}
          sur <span className="font-medium">{filteredData.length}</span> résultats
        </div>

        <div className="flex space-x-2">
          <button
            onClick={() => handlePageChange(Math.max(currentPage - 1, 1))}
            disabled={currentPage === 1}
            className="p-2 rounded-md border disabled:opacity-50"
          >
            <ChevronLeft size={16} />
          </button>
          <div className="flex space-x-1">
            {[...Array(Math.min(totalPages, 5))].map((_, i) => {
              let pageNum: number;
              
              // Logic to handle showing relevant page numbers
              if (totalPages <= 5) {
                pageNum = i + 1;
              } else if (currentPage <= 3) {
                pageNum = i + 1;
              } else if (currentPage >= totalPages - 2) {
                pageNum = totalPages - 4 + i;
              } else {
                pageNum = currentPage - 2 + i;
              }
              
              return (
                <button
                  key={i}
                  onClick={() => handlePageChange(pageNum)}
                  className={cn(
                    "px-3 py-1 rounded-md",
                    pageNum === currentPage
                      ? "bg-blue-600 text-white"
                      : "border hover:bg-gray-100"
                  )}
                >
                  {pageNum}
                </button>
              );
            })}
          </div>
          <button
            onClick={() => handlePageChange(Math.min(currentPage + 1, totalPages))}
            disabled={currentPage === totalPages}
            className="p-2 rounded-md border disabled:opacity-50"
          >
            <ChevronRight size={16} />
          </button>
        </div>
      </div>
    );
  };

  return (
    <div className={cn("bg-white rounded-lg shadow-sm overflow-hidden", className)}>
      {searchable && (
        <div className="p-4 border-b">
          <div className="relative">
            <div className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
              <Search size={18} className="text-gray-400" />
            </div>
            <input
              type="text"
              className="w-full p-2 pl-10 pr-3 border rounded-md focus:ring-blue-500 focus:border-blue-500"
              placeholder="Rechercher..."
              value={searchTerm}
              onChange={(e) => {
                setSearchTerm(e.target.value);
                setCurrentPage(1);
              }}
            />
          </div>
        </div>
      )}

      <div className="overflow-x-auto">
        <table className="min-w-full divide-y divide-gray-200">
          <thead className="bg-gray-50">
            <tr>
              {columns.map((column) => (
                <th
                  key={column.key}
                  scope="col"
                  className={cn(
                    "px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider",
                    column.width
                  )}
                >
                  {column.header}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="bg-white divide-y divide-gray-200">
            {currentData.length === 0 ? (
              <tr>
                <td
                  colSpan={columns.length}
                  className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center"
                >
                  {emptyMessage}
                </td>
              </tr>
            ) : (
              currentData.map((row, rowIndex) => (
                <tr
                  key={rowIndex}
                  className={onRowClick ? "cursor-pointer hover:bg-gray-50" : ""}
                  onClick={() => onRowClick && onRowClick(row)}
                >
                  {columns.map((column) => (
                    <td
                      key={`${rowIndex}-${column.key}`}
                      className="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                    >
                      {column.render
                        ? column.render((row as any)[column.key], row)
                        : (row as any)[column.key]}
                    </td>
                  ))}
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {renderPagination()}
    </div>
  );
}

export default TableDisplay;
