
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Package, PencilLine, Trash2, Plus } from "lucide-react";
import { api } from "@/services/api";
import { Button } from "@/components/ui/button";
import TableDisplay from "@/components/common/TableDisplay";
import { formatCurrency } from "@/lib/utils";

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

  const columns = [
    {
      key: "id",
      header: "ID",
      width: "8%",
    },
    {
      key: "nom",
      header: "Nom",
      width: "18%",
    },
    {
      key: "description",
      header: "Description",
      width: "22%",
    },
    {
      key: "categorie",
      header: "Catégorie",
      width: "12%",
      render: (value: string) => (
        <span className="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
          {value}
        </span>
      ),
    },
    {
      key: "prix",
      header: "Prix",
      width: "10%",
      render: (value: number) => (
        <span className="font-semibold">{formatCurrency(value)}</span>
      ),
    },
    {
      key: "stock",
      header: "Stock",
      width: "10%",
      render: (value: number) => (
        <span className={value < 30 ? "text-red-500 font-medium" : "font-medium"}>
          {value}
        </span>
      ),
    },
    {
      key: "actif",
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
      width: "10%",
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

  const filteredProduits = produits?.filter((produit: any) => 
    produit.nom.toLowerCase().includes(searchTerm.toLowerCase()) || 
    produit.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
    produit.categorie.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Gestion des Produits</h1>
        <Button className="flex items-center gap-2">
          <Plus size={18} />
          Ajouter un produit
        </Button>
      </div>

      {isLoading ? (
        <div className="text-center py-8">Chargement des produits...</div>
      ) : error ? (
        <div className="text-center py-8 text-red-500">{error.toString()}</div>
      ) : (
        <TableDisplay
          data={filteredProduits}
          columns={columns}
          emptyMessage="Aucun produit trouvé"
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
          searchPlaceholder="Rechercher un produit..."
        />
      )}
    </div>
  );
};

export default AdminProduits;
