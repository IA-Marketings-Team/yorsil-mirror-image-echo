
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { UserPlus } from "lucide-react";
import PageHeader from "@/components/common/PageHeader";
import LoadingState from "@/components/common/LoadingState";
import ErrorState from "@/components/common/ErrorState";
import UsersTable from "@/components/admin/users/UsersTable";
import { Button } from "@/components/ui/button";
import { supabaseService } from "@/services/supabase.service";
import { appConfig } from "@/services/mockData";
import { User } from "@/types";

const AdminUsers = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [showAddModal, setShowAddModal] = useState(false);

  const { data: usersData, isLoading, error } = useQuery({
    queryKey: ['adminUsers'],
    queryFn: async () => {
      return await supabaseService.users.getAll();
    }
  });

  // Convert Supabase data to match our User type
  const users: User[] = usersData ? usersData.map(user => ({
    id: user.id,
    email: user.email,
    nom: user.nom,
    prenom: user.prenom || undefined,
    roles: user.roles as any,
    picture: user.picture || undefined,
    sessionToken: user.session_token || undefined
  })) : [];

  const handleAddUser = () => {
    console.log("Add user clicked");
    setShowAddModal(true);
  };

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Utilisateurs" 
        actions={
          <Button onClick={handleAddUser} className="flex items-center gap-2">
            <UserPlus size={16} />
            Ajouter un utilisateur
          </Button>
        }
      />

      {isLoading ? (
        <LoadingState message={appConfig.loadingMessages.users} />
      ) : error ? (
        <ErrorState error={error as Error} message={appConfig.errorMessages.users} />
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
