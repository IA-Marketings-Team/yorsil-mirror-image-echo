
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Store, PlusSquare } from "lucide-react";
import { api } from "@/services/api";
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
    initialData: [
      { id: 1, nom: "Boutique Centrale", adresse: "123 Rue de Paris", telephone: "+33612345678", email: "contact@boutiquecentrale.com", solde: 1250.75, active: true },
      { id: 2, nom: "Point Service", adresse: "45 Avenue Victor Hugo", telephone: "+33612345679", email: "contact@pointservice.com", solde: 850.25, active: true },
      { id: 3, nom: "Tabac du Coin", adresse: "12 Place de la République", telephone: "+33612345680", email: "contact@tabacducoin.com", solde: 2150.00, active: true },
      { id: 4, nom: "Boutique Express", adresse: "78 Boulevard Saint-Michel", telephone: "+33612345681", email: "contact@boutiqueexpress.com", solde: 350.50, active: false },
      { id: 5, nom: "Yorsil Services", adresse: "35 Rue des Martyrs", telephone: "+33612345682", email: "contact@yorsilservices.com", solde: 1750.30, active: true }
    ]
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
        <LoadingState message="Chargement des boutiques..." />
      ) : error ? (
        <ErrorState error={error as Error} message="Erreur de chargement des boutiques" />
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
