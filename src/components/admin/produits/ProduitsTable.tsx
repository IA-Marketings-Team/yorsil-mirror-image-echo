
import React from "react";
import { PencilLine, Trash2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import TableDisplay from "@/components/common/TableDisplay";
import { formatCurrency } from "@/lib/utils";
import { Produit } from "@/types/entities.types";

interface ProduitsTableProps {
  produits: Produit[];
  searchTerm: string;
  onSearchChange: (value: string) => void;
}

const ProduitsTable: React.FC<ProduitsTableProps> = ({ 
  produits, 
  searchTerm, 
  onSearchChange 
}) => {
  const columns = [
    {
      key: "id",
      header: "ID",
      width: "8%",
    },
    {
      key: "nom",
      header: "Nom",
      width: "18%",
    },
    {
      key: "description",
      header: "Description",
      width: "22%",
    },
    {
      key: "categorie",
      header: "Catégorie",
      width: "12%",
      render: (value: string) => (
        <span className="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
          {value}
        </span>
      ),
    },
    {
      key: "prix",
      header: "Prix",
      width: "10%",
      render: (value: number) => (
        <span className="font-semibold">{formatCurrency(value)}</span>
      ),
    },
    {
      key: "stock",
      header: "Stock",
      width: "10%",
      render: (value: number) => (
        <span className={value < 30 ? "text-red-500 font-medium" : "font-medium"}>
          {value}
        </span>
      ),
    },
    {
      key: "actif",
      header: "Statut",
      width: "10%",
      render: (value: boolean) => (
        <span 
          className={`px-2 py-1 rounded-full text-xs font-medium ${
            value ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
          }`}
        >
          {value ? "Actif" : "Inactif"}
        </span>
      ),
    },
    {
      key: "actions",
      header: "Actions",
      width: "10%",
      render: (_: any, row: any) => (
        <div className="flex space-x-2">
          <Button variant="ghost" size="sm" className="h-8 w-8">
            <PencilLine className="h-4 w-4" />
          </Button>
          <Button variant="ghost" size="sm" className="h-8 w-8">
            <Trash2 className="h-4 w-4 text-red-500" />
          </Button>
        </div>
      ),
    },
  ];

  const filteredProduits = produits?.filter((produit: Produit) => 
    produit.nom.toLowerCase().includes(searchTerm.toLowerCase()) || 
    produit.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
    produit.categorie.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <TableDisplay
      data={filteredProduits}
      columns={columns}
      emptyMessage="Aucun produit trouvé"
      searchTerm={searchTerm}
      onSearchChange={onSearchChange}
      searchPlaceholder="Rechercher un produit..."
    />
  );
};

export default ProduitsTable;
