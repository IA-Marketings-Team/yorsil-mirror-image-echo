
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { UserPlus } from "lucide-react";
import { api } from "@/services/api";
import { mockUsers, appConfig } from "@/services/mockData";
import PageHeader from "@/components/common/PageHeader";
import LoadingState from "@/components/common/LoadingState";
import ErrorState from "@/components/common/ErrorState";
import UsersTable from "@/components/admin/users/UsersTable";
import { Button } from "@/components/ui/button";

const AdminUsers = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [showAddModal, setShowAddModal] = useState(false);

  const { data: users, isLoading, error } = useQuery({
    queryKey: ['adminUsers'],
    queryFn: async () => {
      const response = await api.get('/admin/users');
      return response.data;
    },
    initialData: mockUsers
  });

  const handleAddUser = () => {
    console.log("Add user clicked");
  };

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Utilisateurs" 
        actions={
          <Button onClick={() => setShowAddModal(true)} className="flex items-center gap-2">
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
