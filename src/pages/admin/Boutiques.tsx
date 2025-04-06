
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Plus } from "lucide-react";
import { api } from "@/services/api";
import PageHeader from "@/components/common/PageHeader";
import LoadingState from "@/components/common/LoadingState";
import ErrorState from "@/components/common/ErrorState";
import BoutiquesTable from "@/components/admin/boutiques/BoutiquesTable";

const AdminBoutiques = () => {
  const [searchTerm, setSearchTerm] = useState("");
  
  // Fetch boutiques
  const { data: boutiques, isLoading, error } = useQuery({
    queryKey: ['adminBoutiques'],
    queryFn: async () => {
      // In a real app, we would fetch from API
      const response = await api.get('/admin/boutiques');
      return response.data;
    },
    // Mock data for development
    initialData: [
      { id: 1, nom: "Boutique Centrale", adresse: "123 Rue de Paris", telephone: "+33612345678", email: "contact@boutiquecentrale.com", solde: 1250.75, active: true },
      { id: 2, nom: "Point Service", adresse: "45 Avenue Victor Hugo", telephone: "+33612345679", email: "contact@pointservice.com", solde: 850.25, active: true },
      { id: 3, nom: "Tabac du Coin", adresse: "12 Place de la République", telephone: "+33612345680", email: "contact@tabacducoin.com", solde: 2150.00, active: true },
      { id: 4, nom: "Boutique Express", adresse: "78 Boulevard Saint-Michel", telephone: "+33612345681", email: "contact@boutiqueexpress.com", solde: 350.50, active: false },
      { id: 5, nom: "Yorsil Services", adresse: "35 Rue des Martyrs", telephone: "+33612345682", email: "contact@yorsilservices.com", solde: 1750.30, active: true }
    ]
  });

  const handleAddBoutique = () => {
    // Add boutique logic here
    console.log("Add boutique clicked");
  };

  return (
    <div className="space-y-6 p-6">
      <PageHeader 
        title="Gestion des Boutiques"
        action={{
          label: "Ajouter une boutique",
          icon: <Plus size={18} />,
          onClick: handleAddBoutique,
        }}
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
