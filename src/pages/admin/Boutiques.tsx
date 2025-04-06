
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Store, PencilLine, Trash2, Ban, Check, Plus } from "lucide-react";
import { api } from "@/services/api";
import { Button } from "@/components/ui/button";
import TableDisplay from "@/components/common/TableDisplay";
import { formatCurrency } from "@/lib/utils";

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
      key: "adresse",
      header: "Adresse",
      width: "18%",
    },
    {
      key: "telephone",
      header: "Téléphone",
      width: "12%",
    },
    {
      key: "email",
      header: "Email",
      width: "15%",
    },
    {
      key: "solde",
      header: "Solde",
      width: "12%",
      render: (value: number) => (
        <span className="font-semibold">{formatCurrency(value)}</span>
      ),
    },
    {
      key: "active",
      header: "Statut",
      width: "10%",
      render: (value: boolean) => (
        <span 
          className={`px-2 py-1 rounded-full text-xs font-medium ${
            value ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"
          }`}
        >
          {value ? "Active" : "Inactive"}
        </span>
      ),
    },
    {
      key: "actions",
      header: "Actions",
      width: "12%",
      render: (_: any, row: any) => (
        <div className="flex space-x-2">
          <Button variant="ghost" size="sm" className="h-8 w-8">
            <PencilLine className="h-4 w-4" />
          </Button>
          {row.active ? (
            <Button variant="ghost" size="sm" className="h-8 w-8">
              <Ban className="h-4 w-4 text-red-500" />
            </Button>
          ) : (
            <Button variant="ghost" size="sm" className="h-8 w-8">
              <Check className="h-4 w-4 text-green-500" />
            </Button>
          )}
        </div>
      ),
    },
  ];

  const filteredBoutiques = boutiques?.filter((boutique: any) => 
    boutique.nom.toLowerCase().includes(searchTerm.toLowerCase()) || 
    boutique.adresse.toLowerCase().includes(searchTerm.toLowerCase()) ||
    boutique.email.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Gestion des Boutiques</h1>
        <Button className="flex items-center gap-2">
          <Plus size={18} />
          Ajouter une boutique
        </Button>
      </div>

      {isLoading ? (
        <div className="text-center py-8">Chargement des boutiques...</div>
      ) : error ? (
        <div className="text-center py-8 text-red-500">{error.toString()}</div>
      ) : (
        <TableDisplay
          data={filteredBoutiques}
          columns={columns}
          emptyMessage="Aucune boutique trouvée"
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
          searchPlaceholder="Rechercher une boutique..."
        />
      )}
    </div>
  );
};

export default AdminBoutiques;
