
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { User, UserPlus } from "lucide-react";
import { api } from "@/services/api";
import PageHeader from "@/components/common/PageHeader";
import LoadingState from "@/components/common/LoadingState";
import ErrorState from "@/components/common/ErrorState";
import UsersTable from "@/components/admin/users/UsersTable";

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

  const handleAddUser = () => {
    // Add user logic here
    console.log("Add user clicked");
  };

  return (
    <div className="space-y-6 p-6">
      <PageHeader 
        title="Gestion des Utilisateurs"
        action={{
          label: "Ajouter un utilisateur",
          icon: <UserPlus size={18} />,
          onClick: handleAddUser,
        }}
      />

      {isLoading ? (
        <LoadingState message="Chargement des utilisateurs..." />
      ) : error ? (
        <ErrorState error={error as Error} message="Erreur de chargement des utilisateurs" />
      ) : (
        <UsersTable 
          users={users} 
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
        />
      )}
    </div>
  );
};

export default AdminUsers;
