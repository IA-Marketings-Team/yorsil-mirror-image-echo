
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Filter, Download, Eye } from "lucide-react";
import { api } from "@/services/api";
import { Button } from "@/components/ui/button";
import TableDisplay from "@/components/common/TableDisplay";
import { formatCurrency, formatDate } from "@/lib/utils";

const AdminJournal = () => {
  const [searchTerm, setSearchTerm] = useState("");
  const [filterType, setFilterType] = useState("all");
  
  // Fetch transactions
  const { data: transactions, isLoading, error } = useQuery({
    queryKey: ['adminTransactions'],
    queryFn: async () => {
      // In a real app, we would fetch from API
      const response = await api.get('/admin/journal');
      return response.data;
    },
    // Mock data for development
    initialData: [
      { id: 12345, type: "Recharge", date: "2024-04-05T14:30:00", boutique: "Boutique Centrale", client: "Jean Dupont", montant: 50, status: "Success" },
      { id: 12346, type: "Transfert", date: "2024-04-05T13:15:00", boutique: "Tabac du Coin", client: "Marie Martin", montant: 100, status: "Success" },
      { id: 12347, type: "Service", date: "2024-04-05T11:45:00", boutique: "Point Service", client: "Pierre Bernard", montant: 25.50, status: "Pending" },
      { id: 12348, type: "Billeterie", date: "2024-04-04T17:20:00", boutique: "Boutique Express", client: "Sophie Petit", montant: 80, status: "Success" },
      { id: 12349, type: "Recharge", date: "2024-04-04T09:10:00", boutique: "Boutique Centrale", client: "Lucie Thomas", montant: 20, status: "Failed" },
      { id: 12350, type: "Credit", date: "2024-04-03T16:40:00", boutique: "Yorsil Services", client: "Admin", montant: 500, status: "Success" },
      { id: 12351, type: "Debit", date: "2024-04-03T15:20:00", boutique: "Point Service", client: "Admin", montant: 150, status: "Success" },
      { id: 12352, type: "Transfert", date: "2024-04-02T12:10:00", boutique: "Tabac du Coin", client: "Alice Dubois", montant: 75, status: "Success" },
      { id: 12353, type: "Billeterie", date: "2024-04-01T10:05:00", boutique: "Boutique Express", client: "Robert Martin", montant: 120, status: "Canceled" },
      { id: 12354, type: "Service", date: "2024-03-31T09:15:00", boutique: "Yorsil Services", client: "Julie Leroy", montant: 35.25, status: "Success" }
    ]
  });

  const columns = [
    {
      key: "id",
      header: "Référence",
      width: "10%",
      render: (value: any) => <span className="font-medium text-blue-600">#{value}</span>,
    },
    {
      key: "date",
      header: "Date",
      width: "15%",
      render: (value: any) => formatDate(value),
    },
    {
      key: "type",
      header: "Type",
      width: "10%",
      render: (value: any) => {
        let badgeClass = "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ";
        
        switch (value.toLowerCase()) {
          case "recharge":
            badgeClass += "bg-green-100 text-green-700";
            break;
          case "transfert":
            badgeClass += "bg-blue-100 text-blue-700";
            break;
          case "service":
            badgeClass += "bg-purple-100 text-purple-700";
            break;
          case "billeterie":
            badgeClass += "bg-yellow-100 text-yellow-700";
            break;
          case "credit":
            badgeClass += "bg-cyan-100 text-cyan-700";
            break;
          case "debit":
            badgeClass += "bg-pink-100 text-pink-700";
            break;
          default:
            badgeClass += "bg-gray-100 text-gray-700";
        }
        
        return <span className={badgeClass}>{value}</span>;
      },
    },
    {
      key: "boutique",
      header: "Boutique",
      width: "15%",
    },
    {
      key: "client",
      header: "Client",
      width: "15%",
    },
    {
      key: "montant",
      header: "Montant",
      width: "15%",
      render: (value: any) => formatCurrency(value),
    },
    {
      key: "status",
      header: "Statut",
      width: "10%",
      render: (value: any) => {
        let badgeClass = "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ";
        
        switch (value.toLowerCase()) {
          case "success":
          case "completed":
            badgeClass += "bg-green-100 text-green-700";
            break;
          case "pending":
            badgeClass += "bg-yellow-100 text-yellow-700";
            break;
          case "failed":
            badgeClass += "bg-red-100 text-red-700";
            break;
          case "canceled":
            badgeClass += "bg-gray-100 text-gray-700";
            break;
          default:
            badgeClass += "bg-gray-100 text-gray-700";
        }
        
        return <span className={badgeClass}>{value}</span>;
      },
    },
    {
      key: "actions",
      header: "Actions",
      width: "10%",
      render: (_: any, row: any) => (
        <div className="flex space-x-2">
          <Button variant="ghost" size="icon" className="h-8 w-8">
            <Eye className="h-4 w-4" />
          </Button>
          <Button variant="ghost" size="icon" className="h-8 w-8">
            <Download className="h-4 w-4 text-blue-500" />
          </Button>
        </div>
      ),
    },
  ];

  const filteredTransactions = transactions?.filter((transaction: any) => {
    // Filter by search term
    const searchMatch = 
      transaction.boutique.toLowerCase().includes(searchTerm.toLowerCase()) || 
      transaction.client.toLowerCase().includes(searchTerm.toLowerCase()) ||
      String(transaction.id).includes(searchTerm);
    
    // Filter by type
    const typeMatch = filterType === "all" || transaction.type.toLowerCase() === filterType.toLowerCase();
    
    return searchMatch && typeMatch;
  });

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Journal des Transactions</h1>

      <div className="flex flex-wrap gap-4 items-center">
        <div className="flex-grow max-w-md">
          <input
            type="text"
            placeholder="Rechercher..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
            className="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        
        <div className="flex space-x-4">
          <div>
            <label htmlFor="typeFilter" className="block text-sm font-medium text-gray-700 mb-1">Type de transaction</label>
            <select
              id="typeFilter"
              value={filterType}
              onChange={(e) => setFilterType(e.target.value)}
              className="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="all">Tous</option>
              <option value="recharge">Recharge</option>
              <option value="transfert">Transfert</option>
              <option value="service">Service</option>
              <option value="billeterie">Billeterie</option>
              <option value="credit">Crédit</option>
              <option value="debit">Débit</option>
            </select>
          </div>
          
          <div>
            <label htmlFor="dateFilter" className="block text-sm font-medium text-gray-700 mb-1">Période</label>
            <select
              id="dateFilter"
              className="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="today">Aujourd'hui</option>
              <option value="yesterday">Hier</option>
              <option value="week">Cette semaine</option>
              <option value="month" selected>Ce mois</option>
              <option value="custom">Personnalisé</option>
            </select>
          </div>
        </div>
        
        <Button variant="outline" className="flex items-center gap-2 ml-auto">
          <Filter size={16} />
          Filtres avancés
        </Button>
        
        <Button variant="outline" className="flex items-center gap-2">
          <Download size={16} />
          Exporter
        </Button>
      </div>

      {isLoading ? (
        <div className="text-center py-8">Chargement des transactions...</div>
      ) : error ? (
        <div className="text-center py-8 text-red-500">{error.toString()}</div>
      ) : (
        <TableDisplay
          data={filteredTransactions}
          columns={columns}
          emptyMessage="Aucune transaction trouvée"
          searchable={false}
        />
      )}
    </div>
  );
};

export default AdminJournal;
