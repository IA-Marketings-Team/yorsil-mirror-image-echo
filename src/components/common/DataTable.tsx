
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
    icon: React.ReactNode;
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
    <div className="w-full overflow-auto">
      <Table>
        <TableHeader>
          <TableRow>
            {columns.map((column, index) => (
              <TableHead key={index} className={column.sortable ? "cursor-pointer" : ""}>
                {column.header}
                {column.sortable && <span className="ml-2">▼</span>}
              </TableHead>
            ))}
            {showActions && <TableHead>Action</TableHead>}
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
              <TableRow key={rowIndex} onClick={() => onRowClick && onRowClick(row)} className={onRowClick ? "cursor-pointer" : ""}>
                {columns.map((column, colIndex) => (
                  <TableCell key={colIndex}>
                    {column.render ? column.render(row[column.accessor], row) : row[column.accessor]}
                  </TableCell>
                ))}
                {showActions && (
                  <TableCell className="flex gap-1">
                    {actionItems.map((item, i) => (
                      <button 
                        key={i} 
                        onClick={(e) => {
                          e.stopPropagation();
                          item.onClick(row);
                        }}
                        className={i === 0 ? "p-2 bg-green-500 rounded-md" : 
                                  i === 1 ? "p-2 rounded-md" : 
                                  "p-2 bg-yellow-500 rounded-md"}
                      >
                        {item.icon}
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
