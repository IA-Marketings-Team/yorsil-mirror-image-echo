
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Store, PlusSquare } from "lucide-react";
import { api } from "@/services/api";
import { mockBoutiques, appConfig } from "@/services/mockData";
import PageHeader from "@/components/common/PageHeader";
import LoadingState from "@/components/common/LoadingState";
import ErrorState from "@/components/common/ErrorState";
import BoutiquesTable from "@/components/admin/boutiques/BoutiquesTable";
import { Button } from "@/components/ui/button";

const AdminBoutiques = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [showAddModal, setShowAddModal] = useState(false);

  const { data: boutiques, isLoading, error } = useQuery({
    queryKey: ['adminBoutiques'],
    queryFn: async () => {
      const response = await api.get('/admin/boutiques');
      return response.data;
    },
    initialData: mockBoutiques
  });

  const handleAddBoutique = () => {
    console.log("Add boutique clicked");
  };

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Boutiques"
        actions={
          <Button onClick={() => setShowAddModal(true)} className="flex items-center gap-2">
            <Store size={16} />
            <PlusSquare size={16} className="ml-1 mr-1" />
            Ajouter une boutique
          </Button>
        }
      />

      {isLoading ? (
        <LoadingState message={appConfig.loadingMessages.boutiques} />
      ) : error ? (
        <ErrorState error={error as Error} message={appConfig.errorMessages.boutiques} />
      ) : (
        <BoutiquesTable 
          boutiques={boutiques} 
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
        />
      )}
    </div>
  );
};

export default AdminBoutiques;
