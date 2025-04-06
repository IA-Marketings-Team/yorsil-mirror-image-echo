
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { api } from "@/services/api";
import { Calendar, Download, Filter, ChevronDown } from "lucide-react";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import TableDisplay from "@/components/common/TableDisplay";
import { formatCurrency, formatDate } from "@/lib/utils";

const OfficeHistory = () => {
  const [startDate, setStartDate] = useState("");
  const [endDate, setEndDate] = useState("");
  const [transactionType, setTransactionType] = useState("all");
  const [isFilterMenuOpen, setIsFilterMenuOpen] = useState(false);

  // Mock data for when API is not available
  const mockTransactionHistory = [
    {
      id: 12345,
      reference: "TRX-12345",
      type: "Recharge",
      date: "2024-04-05T14:30:00",
      client: "+33612345678",
      montant: 50,
      status: "Success",
      details: {
        operateur: "Orange",
        produit: "Recharge 50€"
      }
    },
    {
      id: 12346,
      reference: "TRX-12346",
      type: "Transfert",
      date: "2024-04-05T13:15:00",
      client: "+33623456789",
      montant: 100,
      status: "Success",
      details: {
        operateur: "SFR",
        beneficiaire: "+33634567890"
      }
    },
    {
      id: 12347,
      reference: "TRX-12347",
      type: "Service",
      date: "2024-04-05T11:45:00",
      client: "Jean Dupont",
      montant: 25.50,
      status: "Success",
      details: {
        service: "Paiement de facture",
        reference: "F20240401-101"
      }
    },
    {
      id: 12348,
      reference: "TRX-12348",
      type: "Billeterie",
      date: "2024-04-04T17:20:00",
      client: "Marie Martin",
      montant: 80,
      status: "Success",
      details: {
        trajet: "Paris → Lyon",
        date: "2024-04-15T10:00:00"
      }
    },
    {
      id: 12349,
      reference: "TRX-12349",
      type: "Recharge",
      date: "2024-04-04T09:10:00",
      client: "+33656789012",
      montant: 20,
      status: "Failed",
      details: {
        operateur: "Bouygues",
        produit: "Recharge 20€",
        erreur: "Solde insuffisant"
      }
    },
  ];

  // Fetch transaction history with filters
  const { data: transactions, isLoading, refetch } = useQuery({
    queryKey: ['transactionHistory', startDate, endDate, transactionType],
    queryFn: async () => {
      // In a real app, this would call the API with filters
      // const response = await api.get('/history', {
      //   params: {
      //     startDate,
      //     endDate,
      //     type: transactionType !== 'all' ? transactionType : undefined
      //   }
      // });
      // return response.data;

      // Simulate API call with filters
      return mockTransactionHistory.filter(tx => {
        if (transactionType !== 'all' && tx.type.toLowerCase() !== transactionType.toLowerCase()) {
          return false;
        }
        
        if (startDate && new Date(tx.date) < new Date(startDate)) {
          return false;
        }
        
        if (endDate && new Date(tx.date) > new Date(endDate)) {
          return false;
        }
        
        return true;
      });
    },
    initialData: mockTransactionHistory,
  });

  const handleApplyFilters = () => {
    refetch();
    setIsFilterMenuOpen(false);
  };

  const handleExportCSV = () => {
    // In a real app, this would generate and download a CSV file
    alert("Fonctionnalité d'export CSV en cours d'implémentation");
  };

  const columns = [
    {
      key: "reference",
      header: "Référence",
      render: (value: string) => <span className="font-medium text-blue-600">{value}</span>,
    },
    {
      key: "type",
      header: "Type",
      render: (value: string) => {
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
          default:
            badgeClass += "bg-gray-100 text-gray-700";
        }
        
        return <span className={badgeClass}>{value}</span>;
      },
    },
    {
      key: "date",
      header: "Date",
      render: (value: string) => formatDate(value),
    },
    {
      key: "client",
      header: "Client/Bénéficiaire",
    },
    {
      key: "montant",
      header: "Montant",
      render: (value: number) => formatCurrency(value),
    },
    {
      key: "status",
      header: "Statut",
      render: (value: string) => {
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
          default:
            badgeClass += "bg-gray-100 text-gray-700";
        }
        
        return <span className={badgeClass}>{value}</span>;
      },
    },
  ];

  // Calculate summary statistics
  const summary = {
    total: transactions?.reduce((acc, tx) => acc + tx.montant, 0) || 0,
    count: transactions?.length || 0,
    successCount: transactions?.filter(tx => tx.status.toLowerCase() === 'success').length || 0,
    failedCount: transactions?.filter(tx => tx.status.toLowerCase() === 'failed').length || 0,
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Journal des Transactions</h1>
        
        <div className="flex items-center gap-2">
          <div className="relative">
            <Button
              variant="outline"
              className="flex items-center gap-2"
              onClick={() => setIsFilterMenuOpen(!isFilterMenuOpen)}
            >
              <Filter size={18} />
              Filtrer
              <ChevronDown size={16} />
            </Button>
            
            {isFilterMenuOpen && (
              <div className="absolute right-0 mt-2 w-72 bg-white rounded-md shadow-lg z-10 p-4 border">
                <h3 className="text-sm font-medium mb-3">Filtres</h3>
                
                <div className="space-y-3">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                      Date de début
                    </label>
                    <div className="relative">
                      <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Calendar size={16} className="text-gray-400" />
                      </div>
                      <Input
                        type="date"
                        value={startDate}
                        onChange={(e) => setStartDate(e.target.value)}
                        className="pl-10"
                      />
                    </div>
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                      Date de fin
                    </label>
                    <div className="relative">
                      <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Calendar size={16} className="text-gray-400" />
                      </div>
                      <Input
                        type="date"
                        value={endDate}
                        onChange={(e) => setEndDate(e.target.value)}
                        className="pl-10"
                      />
                    </div>
                  </div>
                  
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-1">
                      Type de transaction
                    </label>
                    <select
                      value={transactionType}
                      onChange={(e) => setTransactionType(e.target.value)}
                      className="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                      <option value="all">Toutes</option>
                      <option value="recharge">Recharge</option>
                      <option value="transfert">Transfert</option>
                      <option value="service">Service</option>
                      <option value="billeterie">Billeterie</option>
                    </select>
                  </div>
                  
                  <div className="pt-2 flex justify-end">
                    <Button
                      size="sm"
                      onClick={handleApplyFilters}
                    >
                      Appliquer
                    </Button>
                  </div>
                </div>
              </div>
            )}
          </div>
          
          <Button 
            variant="outline" 
            className="flex items-center gap-2"
            onClick={handleExportCSV}
          >
            <Download size={18} />
            Exporter
          </Button>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div className="bg-white p-4 rounded-lg shadow-sm">
          <p className="text-sm text-gray-500">Total des transactions</p>
          <p className="text-2xl font-bold">{formatCurrency(summary.total)}</p>
        </div>
        
        <div className="bg-white p-4 rounded-lg shadow-sm">
          <p className="text-sm text-gray-500">Nombre de transactions</p>
          <p className="text-2xl font-bold">{summary.count}</p>
        </div>
        
        <div className="bg-white p-4 rounded-lg shadow-sm">
          <p className="text-sm text-gray-500">Transactions réussies</p>
          <p className="text-2xl font-bold text-green-600">{summary.successCount}</p>
        </div>
        
        <div className="bg-white p-4 rounded-lg shadow-sm">
          <p className="text-sm text-gray-500">Transactions échouées</p>
          <p className="text-2xl font-bold text-red-600">{summary.failedCount}</p>
        </div>
      </div>

      <div className="bg-white rounded-lg shadow">
        {isLoading ? (
          <div className="text-center py-8">Chargement des transactions...</div>
        ) : (
          <TableDisplay
            data={transactions || []}
            columns={columns}
            searchable={true}
            emptyMessage="Aucune transaction trouvée"
          />
        )}
      </div>
    </div>
  );
};

export default OfficeHistory;
