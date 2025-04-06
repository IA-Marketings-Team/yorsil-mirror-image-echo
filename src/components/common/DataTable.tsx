
import React from "react";
import { 
  Table, 
  TableBody, 
  TableCell, 
  TableHead, 
  TableHeader, 
  TableRow 
} from "@/components/ui/table";
import { Eye, Check, Trash2 } from "lucide-react";

export interface Column {
  header: string;
  accessor: string;
  render?: (value: any, row: any) => React.ReactNode;
  sortable?: boolean;
}

interface DataTableProps {
  columns: Column[];
  data: any[];
  onRowClick?: (row: any) => void;
  showActions?: boolean;
  actionItems?: {
    icon: React.ReactNode | ((row: any) => React.ReactNode);
    onClick: (row: any) => void;
  }[];
}

const DataTable: React.FC<DataTableProps> = ({ 
  columns, 
  data, 
  onRowClick, 
  showActions = false,
  actionItems = [
    { icon: <Check className="text-white" />, onClick: () => {} },
    { icon: <Eye />, onClick: () => {} },
    { icon: <Trash2 className="text-white" />, onClick: () => {} },
  ]
}) => {
  return (
    <div className="w-full overflow-auto border rounded-md">
      <Table>
        <TableHeader>
          <TableRow className="bg-gray-50">
            {columns.map((column, index) => (
              <TableHead 
                key={index} 
                className={`${column.sortable ? "cursor-pointer" : ""} text-gray-700 font-semibold py-3 px-4`}
              >
                {column.header}
                {column.sortable && <span className="ml-2 text-xs">▼</span>}
              </TableHead>
            ))}
            {showActions && <TableHead className="text-gray-700 font-semibold py-3 px-4">Action</TableHead>}
          </TableRow>
        </TableHeader>
        <TableBody>
          {data.length === 0 ? (
            <TableRow>
              <TableCell colSpan={columns.length + (showActions ? 1 : 0)} className="text-center py-6">
                Aucune donnée disponible dans le tableau
              </TableCell>
            </TableRow>
          ) : (
            data.map((row, rowIndex) => (
              <TableRow 
                key={rowIndex} 
                onClick={() => onRowClick && onRowClick(row)} 
                className={`${onRowClick ? "cursor-pointer" : ""} ${rowIndex % 2 === 0 ? "bg-white" : "bg-gray-50"} hover:bg-gray-100`}
              >
                {columns.map((column, colIndex) => (
                  <TableCell key={colIndex} className="py-3 px-4">
                    {column.render ? column.render(row[column.accessor], row) : row[column.accessor]}
                  </TableCell>
                ))}
                {showActions && (
                  <TableCell className="flex gap-1 py-2 px-4">
                    {actionItems.map((item, i) => (
                      <button 
                        key={i} 
                        onClick={(e) => {
                          e.stopPropagation();
                          item.onClick(row);
                        }}
                        className={i === 0 ? "p-2 bg-green-500 rounded-md hover:bg-green-600" : 
                                  i === 1 ? "p-2 rounded-md border border-gray-300 hover:bg-gray-100" : 
                                  "p-2 bg-red-500 rounded-md hover:bg-red-600"}
                      >
                        {typeof item.icon === "function" ? item.icon(row) : item.icon}
                      </button>
                    ))}
                  </TableCell>
                )}
              </TableRow>
            ))
          )}
        </TableBody>
      </Table>
    </div>
  );
};

export default DataTable;
