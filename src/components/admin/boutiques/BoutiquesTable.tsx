
import React from "react";
import { PencilLine, Ban, Check } from "lucide-react";
import { Button } from "@/components/ui/button";
import TableDisplay from "@/components/common/TableDisplay";
import { formatCurrency } from "@/lib/utils";
import { Boutique } from "@/types/entities.types";

interface BoutiquesTableProps {
  boutiques: Boutique[];
  searchTerm: string;
  onSearchChange: (value: string) => void;
}

const BoutiquesTable: React.FC<BoutiquesTableProps> = ({ 
  boutiques, 
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
      key: "adresse",
      header: "Adresse",
      width: "18%",
    },
    {
      key: "telephone",
      header: "Téléphone",
      width: "12%",
    },
    {
      key: "email",
      header: "Email",
      width: "15%",
    },
    {
      key: "solde",
      header: "Solde",
      width: "12%",
      render: (value: number) => (
        <span className="font-semibold">{formatCurrency(value)}</span>
      ),
    },
    {
      key: "active",
      header: "Statut",
      width: "10%",
      render: (value: boolean) => (
        <span 
          className={`px-2 py-1 rounded-full text-xs font-medium ${
            value ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
          }`}
        >
          {value ? "Active" : "Inactive"}
        </span>
      ),
    },
    {
      key: "actions",
      header: "Actions",
      width: "12%",
      render: (_: any, row: any) => (
        <div className="flex space-x-2">
          <Button variant="ghost" size="sm" className="h-8 w-8">
            <PencilLine className="h-4 w-4" />
          </Button>
          {row.active ? (
            <Button variant="ghost" size="sm" className="h-8 w-8">
              <Ban className="h-4 w-4 text-red-500" />
            </Button>
          ) : (
            <Button variant="ghost" size="sm" className="h-8 w-8">
              <Check className="h-4 w-4 text-green-500" />
            </Button>
          )}
        </div>
      ),
    },
  ];

  const filteredBoutiques = boutiques?.filter((boutique: Boutique) => 
    boutique.nom.toLowerCase().includes(searchTerm.toLowerCase()) || 
    boutique.adresse?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    boutique.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <TableDisplay
      data={filteredBoutiques}
      columns={columns}
      emptyMessage="Aucune boutique trouvée"
      searchTerm={searchTerm}
      onSearchChange={onSearchChange}
      searchPlaceholder="Rechercher une boutique..."
    />
  );
};

export default BoutiquesTable;
