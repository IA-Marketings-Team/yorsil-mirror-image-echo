
export interface TableColumn<T = any> {
  key: string;
  header: string;
  width?: string;
  render?: (value: any, row: T) => React.ReactNode;
}

export interface TableDisplayProps<T = any> {
  data: T[];
  columns: TableColumn<T>[];
  emptyMessage?: string;
  searchTerm?: string;
  onSearchChange?: (value: string) => void;
  searchPlaceholder?: string;
  searchable?: boolean; // Ajout de cette propriété manquante
}
