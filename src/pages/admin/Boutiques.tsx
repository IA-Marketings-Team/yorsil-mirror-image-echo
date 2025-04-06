
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Store, PlusSquare } from "lucide-react";
import PageHeader from "@/components/common/PageHeader";
import LoadingState from "@/components/common/LoadingState";
import ErrorState from "@/components/common/ErrorState";
import BoutiquesTable from "@/components/admin/boutiques/BoutiquesTable";
import { Button } from "@/components/ui/button";
import { supabaseService } from "@/services/supabase.service";
import { appConfig } from "@/services/mockData";

const AdminBoutiques = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [showAddModal, setShowAddModal] = useState(false);

  const { data: boutiques, isLoading, error } = useQuery({
    queryKey: ['adminBoutiques'],
    queryFn: async () => {
      return await supabaseService.boutiques.getAll();
    }
  });

  const handleAddBoutique = () => {
    console.log("Add boutique clicked");
    setShowAddModal(true);
  };

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Boutiques"
        actions={
          <Button onClick={handleAddBoutique} className="flex items-center gap-2">
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
          boutiques={boutiques || []} 
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
        />
      )}
    </div>
  );
};

export default AdminBoutiques;
