
import { useEffect, useState } from "react";
import { useOperateursStore } from "@/stores/operateursStore";
import { usePaysStore } from "@/stores/paysStore";
import TableDisplay from "@/components/common/TableDisplay";
import { Button } from "@/components/ui/button";
import { PlusCircle, Pencil } from "lucide-react";
import { Input } from "@/components/ui/input";

const AdminOperateurs = () => {
  const { operateurs, loading, error, fetchOperateurs } = useOperateursStore();
  const { pays, fetchPays } = usePaysStore();
  
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [currentOperateur, setCurrentOperateur] = useState<any>(null);
  
  useEffect(() => {
    fetchOperateurs();
    fetchPays();
  }, [fetchOperateurs, fetchPays]);
  
  const columns = [
    {
      key: "logo",
      header: "Logo",
      width: "10%",
      render: (value: string) => (
        <div className="flex justify-center">
          {value ? (
            <img 
              src={`/uploads/images/logo/operateur/${value}`} 
              alt="Logo opérateur" 
              className="h-10 w-10 object-contain"
            />
          ) : (
            <div className="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
              <span className="text-gray-500 text-xs">No logo</span>
            </div>
          )}
        </div>
      ),
    },
    {
      key: "nom",
      header: "Nom",
      width: "20%",
    },
    {
      key: "type",
      header: "Type",
      width: "15%",
    },
    {
      key: "nomPays",
      header: "Pays",
      width: "15%",
    },
    {
      key: "longueurCode",
      header: "Longueur Code",
      width: "15%",
      render: (value: number) => value || "-",
    },
    {
      key: "actif",
      header: "Status",
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
      key: "id",
      header: "Actions",
      width: "15%",
      render: (_: any, row: any) => (
        <div className="flex items-center space-x-2">
          <Button
            variant="ghost"
            size="sm"
            onClick={() => handleEdit(row)}
            className="hover:bg-transparent p-0"
          >
            <Pencil size={18} className="text-blue-500" />
          </Button>
        </div>
      ),
    },
  ];
  
  const handleEdit = (operateur: any) => {
    setCurrentOperateur(operateur);
    setIsModalOpen(true);
  };
  
  const handleAdd = () => {
    setCurrentOperateur(null);
    setIsModalOpen(true);
  };

  // Mock for the modal that would be implemented in a real app
  const OperateurModal = () => (
    <div className="p-4 border rounded-lg bg-white shadow-md">
      <h2 className="text-xl font-semibold mb-4">
        {currentOperateur ? "Modifier l'opérateur" : "Ajouter un opérateur"}
      </h2>
      
      <div className="space-y-4">
        <div>
          <label className="block text-sm font-medium text-gray-700">Nom</label>
          <Input
            type="text"
            defaultValue={currentOperateur?.nom || ""}
            placeholder="Nom de l'opérateur"
          />
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700">Type</label>
          <Input
            type="text"
            defaultValue={currentOperateur?.type || ""}
            placeholder="Type d'opérateur"
          />
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700">Pays</label>
          <select className="w-full border rounded-md px-3 py-2">
            <option value="">Sélectionner un pays</option>
            {pays.map((p) => (
              <option 
                key={p.id} 
                value={p.id}
                selected={currentOperateur?.idPays?.id === p.id}
              >
                {p.nom}
              </option>
            ))}
          </select>
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700">Longueur Code</label>
          <Input
            type="number"
            defaultValue={currentOperateur?.longueurCode || ""}
            placeholder="Longueur du code"
          />
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700">Logo</label>
          <Input type="file" />
        </div>
        
        <div className="flex items-center">
          <input
            type="checkbox"
            defaultChecked={currentOperateur?.actif || false}
            className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
          <label className="ml-2 block text-sm text-gray-900">
            Opérateur actif
          </label>
        </div>
      </div>
      
      <div className="flex justify-end space-x-3 mt-6">
        <Button variant="outline" onClick={() => setIsModalOpen(false)}>
          Annuler
        </Button>
        <Button onClick={() => setIsModalOpen(false)}>
          {currentOperateur ? "Modifier" : "Ajouter"}
        </Button>
      </div>
    </div>
  );

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Gestion des Opérateurs</h1>
        <Button onClick={handleAdd} className="flex items-center gap-2">
          <PlusCircle size={18} />
          Ajouter un opérateur
        </Button>
      </div>

      {loading ? (
        <div className="text-center py-8">Chargement des opérateurs...</div>
      ) : error ? (
        <div className="text-center py-8 text-red-500">{error}</div>
      ) : (
        <TableDisplay
          data={operateurs}
          columns={columns}
          emptyMessage="Aucun opérateur trouvé"
        />
      )}
      
      {isModalOpen && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
          <OperateurModal />
        </div>
      )}
    </div>
  );
};

export default AdminOperateurs;
