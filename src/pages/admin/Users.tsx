
import { useState, useEffect } from "react";
import { useQuery } from "@tanstack/react-query";
import { User, PencilLine, Trash2, UserPlus } from "lucide-react";
import { api } from "@/services/api";
import { Button } from "@/components/ui/button";
import TableDisplay from "@/components/common/TableDisplay";

const AdminUsers = () => {
  const [searchTerm, setSearchTerm] = useState("");
  
  // Fetch users
  const { data: users, isLoading, error } = useQuery({
    queryKey: ['adminUsers'],
    queryFn: async () => {
      // In a real app, we would fetch from API
      const response = await api.get('/admin/users');
      return response.data;
    },
    // Mock data for development
    initialData: [
      { id: 1, nom: "Dupont", prenom: "Jean", email: "jean.dupont@example.com", telephone: "+33612345678", roles: ["ROLE_ADMIN"], isActive: true },
      { id: 2, nom: "Martin", prenom: "Lucie", email: "lucie.martin@example.com", telephone: "+33612345679", roles: ["ROLE_ADMIN"], isActive: true },
      { id: 3, nom: "Bernard", prenom: "Pierre", email: "pierre.bernard@example.com", telephone: "+33612345680", roles: ["ROLE_BOUT"], isActive: true },
      { id: 4, nom: "Thomas", prenom: "Marie", email: "marie.thomas@example.com", telephone: "+33612345681", roles: ["ROLE_BOUT"], isActive: false },
      { id: 5, nom: "Petit", prenom: "Sophie", email: "sophie.petit@example.com", telephone: "+33612345682", roles: ["ROLE_USER"], isActive: true }
    ]
  });

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
    user.prenom.toLowerCase().includes(searchTerm.toLowerCase()) ||
    user.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Gestion des Utilisateurs</h1>
        <Button className="flex items-center gap-2">
          <UserPlus size={18} />
          Ajouter un utilisateur
        </Button>
      </div>

      {isLoading ? (
        <div className="text-center py-8">Chargement des utilisateurs...</div>
      ) : error ? (
        <div className="text-center py-8 text-red-500">{error.toString()}</div>
      ) : (
        <TableDisplay
          data={filteredUsers}
          columns={columns}
          emptyMessage="Aucun utilisateur trouvé"
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
          searchPlaceholder="Rechercher un utilisateur..."
        />
      )}
    </div>
  );
};

export default AdminUsers;
