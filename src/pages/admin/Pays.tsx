
import { useEffect, useState } from "react";
import { usePaysStore } from "@/stores/paysStore";
import TableDisplay from "@/components/common/TableDisplay";
import { Check, X } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

const AdminPays = () => {
  const { pays, loading, error, fetchPays, updatePaysApi } = usePaysStore();
  const [searchTerm, setSearchTerm] = useState("");
  const [selectedApiType, setSelectedApiType] = useState("diaspo");

  useEffect(() => {
    fetchPays();
  }, [fetchPays]);

  const handleApiChange = async (id: number, isApi: boolean) => {
    await updatePaysApi(id, isApi, isApi ? selectedApiType : undefined);
  };

  const columns = [
    {
      key: "id",
      header: "ID",
      width: "10%",
    },
    {
      key: "nom",
      header: "Nom du pays",
      width: "30%",
    },
    {
      key: "code",
      header: "Code",
      width: "15%",
    },
    {
      key: "isApi",
      header: "API",
      width: "15%",
      render: (value: boolean, row: any) => (
        <div className="flex justify-center">
          <input
            type="checkbox"
            checked={value}
            onChange={() => handleApiChange(row.id, !value)}
            className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
          />
        </div>
      ),
    },
    {
      key: "typeApi",
      header: "Type API",
      width: "30%",
      render: (value: string) => (
        <span className="font-medium">{value || "-"}</span>
      ),
    },
  ];

  const filteredPays = pays.filter((pay) =>
    pay.nom.toLowerCase().includes(searchTerm.toLowerCase())
  );

  // Function to check/uncheck all countries
  const handleCheckAll = (checked: boolean) => {
    pays.forEach((pay) => {
      updatePaysApi(pay.id, checked, checked ? selectedApiType : undefined);
    });
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Gestion des Pays</h1>
        <div className="flex items-center space-x-4">
          <select 
            className="border rounded-md px-3 py-2"
            value={selectedApiType}
            onChange={(e) => setSelectedApiType(e.target.value)}
          >
            <option value="diaspo">Diaspora</option>
            <option value="local">Local</option>
          </select>
          
          <Button
            variant="outline"
            className="flex items-center gap-2"
            onClick={() => handleCheckAll(true)}
          >
            <Check size={18} className="text-green-500" />
            Tout sélectionner
          </Button>
          
          <Button
            variant="outline"
            className="flex items-center gap-2"
            onClick={() => handleCheckAll(false)}
          >
            <X size={18} className="text-red-500" />
            Tout désélectionner
          </Button>
        </div>
      </div>

      <div className="flex items-center">
        <Input
          type="text"
          placeholder="Rechercher un pays..."
          value={searchTerm}
          onChange={(e) => setSearchTerm(e.target.value)}
          className="max-w-md"
        />
      </div>

      {loading ? (
        <div className="text-center py-8">Chargement des pays...</div>
      ) : error ? (
        <div className="text-center py-8 text-red-500">{error}</div>
      ) : (
        <TableDisplay
          data={filteredPays}
          columns={columns}
          emptyMessage="Aucun pays trouvé"
          searchable={false}
        />
      )}
    </div>
  );
};

export default AdminPays;
