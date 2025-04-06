
import React from "react";
import { PencilLine, Trash2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import TableDisplay from "@/components/common/TableDisplay";
import { User } from "@/types/auth.types";

interface UsersTableProps {
  users: User[];
  searchTerm: string;
  onSearchChange: (value: string) => void;
}

const UsersTable: React.FC<UsersTableProps> = ({ 
  users, 
  searchTerm, 
  onSearchChange 
}) => {
  const columns = [
    {
      key: "id",
      header: "ID",
      width: "10%",
    },
    {
      key: "nom",
      header: "Nom",
      width: "15%",
    },
    {
      key: "prenom",
      header: "Prénom",
      width: "15%",
    },
    {
      key: "email",
      header: "Email",
      width: "20%",
    },
    {
      key: "telephone",
      header: "Téléphone",
      width: "15%",
    },
    {
      key: "roles",
      header: "Rôle",
      width: "10%",
      render: (value: string[]) => (
        <span className="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
          {value.includes("ROLE_ADMIN") 
            ? "Admin" 
            : value.includes("ROLE_BOUT") 
              ? "Boutique" 
              : "Utilisateur"
          }
        </span>
      ),
    },
    {
      key: "isActive",
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
      width: "15%",
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

  const filteredUsers = users?.filter((user: any) => 
    user.nom.toLowerCase().includes(searchTerm.toLowerCase()) || 
    user.prenom?.toLowerCase().includes(searchTerm.toLowerCase()) ||
    user.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <TableDisplay
      data={filteredUsers}
      columns={columns}
      emptyMessage="Aucun utilisateur trouvé"
      searchTerm={searchTerm}
      onSearchChange={onSearchChange}
      searchPlaceholder="Rechercher un utilisateur..."
    />
  );
};

export default UsersTable;
