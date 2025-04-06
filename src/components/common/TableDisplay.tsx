
import { useState } from "react";
import { Input } from "@/components/ui/input";
import { Search } from "lucide-react";
import { TableDisplayProps } from "@/types/table.types";

const TableDisplay = <T extends object>({
  data,
  columns,
  emptyMessage = "No data found",
  searchTerm = "",
  onSearchChange,
  searchPlaceholder = "Search...",
}: TableDisplayProps<T>) => {
  const [currentPage, setCurrentPage] = useState(1);
  const itemsPerPage = 10;

  // Calculate pagination
  const indexOfLastItem = currentPage * itemsPerPage;
  const indexOfFirstItem = indexOfLastItem - itemsPerPage;
  const currentItems = data.slice(indexOfFirstItem, indexOfLastItem);
  const totalPages = Math.ceil(data.length / itemsPerPage);

  // Pagination handlers
  const goToPage = (pageNumber: number) => {
    setCurrentPage(Math.max(1, Math.min(pageNumber, totalPages)));
  };

  return (
    <div className="bg-white rounded-md shadow">
      {/* Search bar */}
      {onSearchChange && (
        <div className="p-4 border-b">
          <div className="relative">
            <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size={18} />
            <Input
              className="pl-10"
              type="text"
              placeholder={searchPlaceholder}
              value={searchTerm}
              onChange={(e) => onSearchChange(e.target.value)}
            />
          </div>
        </div>
      )}

      {/* Table */}
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="bg-gray-50">
              {columns.map((column) => (
                <th
                  key={column.key}
                  className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                  style={{ width: column.width }}
                >
                  {column.header}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="divide-y divide-gray-200">
            {currentItems.length > 0 ? (
              currentItems.map((item: any, index) => (
                <tr key={index} className="hover:bg-gray-50">
                  {columns.map((column) => (
                    <td key={column.key} className="px-4 py-3 text-sm text-gray-500">
                      {column.render
                        ? column.render(item[column.key], item)
                        : item[column.key]}
                    </td>
                  ))}
                </tr>
              ))
            ) : (
              <tr>
                <td
                  colSpan={columns.length}
                  className="px-4 py-8 text-center text-gray-500"
                >
                  {emptyMessage}
                </td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Pagination */}
      {data.length > itemsPerPage && (
        <div className="px-4 py-3 flex items-center justify-between border-t">
          <div>
            <p className="text-sm text-gray-700">
              Showing{" "}
              <span className="font-medium">
                {indexOfFirstItem + 1}-{Math.min(indexOfLastItem, data.length)}
              </span>{" "}
              of <span className="font-medium">{data.length}</span> results
            </p>
          </div>
          <div className="flex space-x-1">
            <button
              onClick={() => goToPage(currentPage - 1)}
              disabled={currentPage === 1}
              className="px-3 py-1 rounded-md bg-gray-100 text-gray-700 disabled:opacity-50"
            >
              Previous
            </button>
            <button
              onClick={() => goToPage(currentPage + 1)}
              disabled={currentPage === totalPages}
              className="px-3 py-1 rounded-md bg-gray-100 text-gray-700 disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default TableDisplay;
