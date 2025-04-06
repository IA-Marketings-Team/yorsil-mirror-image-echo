
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Tag, PencilLine, Trash2, Plus } from "lucide-react";
import { api } from "@/services/api";
import { Button } from "@/components/ui/button";
import TableDisplay from "@/components/common/TableDisplay";
import { formatCurrency } from "@/lib/utils";

const AdminOffres = () => {
  const [searchTerm, setSearchTerm] = useState("");
  
  // Fetch offres
  const { data: offres, isLoading, error } = useQuery({
    queryKey: ['adminOffres'],
    queryFn: async () => {
      // In a real app, we would fetch from API
      const response = await api.get('/admin/offres');
      return response.data;
    },
    // Mock data for development
    initialData: [
      { id: 1, nom: "Pack Internet 10Go", description: "Forfait Internet 10Go valable 30 jours", operateur: "Orange", type: "Internet", prix: 9.99, validite: 30, actif: true },
      { id: 2, nom: "Forfait Appels 2h", description: "Forfait 2h d'appel valable 15 jours", operateur: "SFR", type: "Voix", prix: 5.00, validite: 15, actif: true },
      { id: 3, nom: "Pack Tout Inclus", description: "Appels, SMS et Internet illimités", operateur: "Bouygues", type: "Tout inclus", prix: 19.99, validite: 30, actif: true },
      { id: 4, nom: "Pack Week-end", description: "Internet illimité pendant le week-end", operateur: "Free", type: "Internet", prix: 4.99, validite: 2, actif: false },
      { id: 5, nom: "Recharge Internationale", description: "Appels internationaux à tarifs réduits", operateur: "Orange", type: "International", prix: 15.00, validite: 30, actif: true }
    ]
  });

  const columns = [
    {
      key: "id",
      header: "ID",
      width: "6%",
    },
    {
      key: "nom",
      header: "Nom",
      width: "15%",
    },
    {
      key: "description",
      header: "Description",
      width: "20%",
    },
    {
      key: "operateur",
      header: "Opérateur",
      width: "12%",
      render: (value: string) => (
        <span className="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
          {value}
        </span>
      ),
    },
    {
      key: "type",
      header: "Type",
      width: "10%",
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
      key: "validite",
      header: "Validité",
      width: "10%",
      render: (value: number) => (
        <span>{value} jours</span>
      ),
    },
    {
      key: "actif",
      header: "Statut",
      width: "8%",
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
      width: "9%",
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

  const filteredOffres = offres?.filter((offre: any) => 
    offre.nom.toLowerCase().includes(searchTerm.toLowerCase()) || 
    offre.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
    offre.operateur.toLowerCase().includes(searchTerm.toLowerCase()) ||
    offre.type.toLowerCase().includes(searchTerm.toLowerCase())
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Gestion des Offres</h1>
        <Button className="flex items-center gap-2">
          <Plus size={18} />
          Ajouter une offre
        </Button>
      </div>

      {isLoading ? (
        <div className="text-center py-8">Chargement des offres...</div>
      ) : error ? (
        <div className="text-center py-8 text-red-500">{error.toString()}</div>
      ) : (
        <TableDisplay
          data={filteredOffres}
          columns={columns}
          emptyMessage="Aucune offre trouvée"
          searchTerm={searchTerm}
          onSearchChange={setSearchTerm}
          searchPlaceholder="Rechercher une offre..."
        />
      )}
    </div>
  );
};

export default AdminOffres;
