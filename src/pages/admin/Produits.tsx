
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Plus } from "lucide-react";
import { api } from "@/services/api";
import PageHeader from "@/components/common/PageHeader";
import LoadingState from "@/components/common/LoadingState";
import ErrorState from "@/components/common/ErrorState";
import ProduitsTable from "@/components/admin/produits/ProduitsTable";

const AdminProduits = () => {
  const [searchTerm, setSearchTerm] = useState("");
  
  // Fetch produits
  const { data: produits, isLoading, error } = useQuery({
    queryKey: ['adminProduits'],
    queryFn: async () => {
      // In a real app, we would fetch from API
      const response = await api.get('/admin/produits');
      return response.data;
    },
    // Mock data for development
    initialData: [
      { id: 1, nom: "Carte SIM Orange", description: "Carte SIM prépayée Orange", categorie: "Téléphonie", prix: 9.99, stock: 50, actif: true },
      { id: 2, nom: "Carte de recharge 10€", description: "Carte de recharge Orange 10€", categorie: "Recharge", prix: 10.00, stock: 100, actif: true },
      { id: 3, nom: "Carte de recharge 20€", description: "Carte de recharge SFR 20€", categorie: "Recharge", prix: 20.00, stock: 75, actif: true },
      { id: 4, nom: "Carte de transport", description: "Carte de transport urbain", categorie: "Transport", prix: 5.00, stock: 25, actif: false },
      { id: 5, nom: "Carte cadeau Steam 50€", description: "Carte cadeau pour plateforme Steam", categorie: "Jeux", prix: 50.00, stock: 30, actif: true }
    ]
  });

  const handleAddProduit = () => {
    // Add produit logic here
    console.log("Add produit clicked");
  };

  return (
    <div className="space-y-6 p-6">
      <PageHeader 
        title="Gestion des Produits"
        action={{
          label: "Ajouter un produit",
          icon: <Plus size={18} />,
          onClick: handleAddProduit,
        }}
      />

      {isLoading ? (
        <LoadingState message="Chargement des produits..." />
      ) : error ? (
        <ErrorState error={error as Error} message="Erreur de chargement des produits" />
      ) : (
        <ProduitsTable 
          produits={produits} 
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
        />
      )}
    </div>
  );
};

export default AdminProduits;
