
import { useEffect, useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { CreditCard, ArrowLeftRight, ShoppingCart, TicketCheck } from "lucide-react";
import StatsCard from "@/components/common/StatsCard";
import DataDisplay from "@/components/common/DataDisplay";
import TableDisplay from "@/components/common/TableDisplay";
import { formatCurrency, formatDate } from "@/lib/utils";
import { api } from "@/services/api";
import { useAuth } from "@/contexts/AuthContext";

const OfficeDashboard = () => {
  const { authState } = useAuth();
  const [balance, setBalance] = useState(0);
  
  // Fetch dashboard stats
  const { data: stats } = useQuery({
    queryKey: ['officeDashboardStats'],
    queryFn: async () => {
      const response = await api.get('/office/dashboard/stats');
      return response.data;
    }
  });

  // Fetch recent transactions
  const { data: recentTransactions } = useQuery({
    queryKey: ['officeRecentTransactions'],
    queryFn: async () => {
      const response = await api.get('/office/recent-transactions');
      return response.data;
    }
  });

  // Fetch boutique balance
  const { data: balanceData } = useQuery({
    queryKey: ['boutiqueBalance'],
    queryFn: async () => {
      const response = await api.get('/office/balance');
      return response.data;
    }
  });

  useEffect(() => {
    if (balanceData) {
      setBalance(balanceData.balance);
    }
  }, [balanceData]);

  // Mock data for when API is not available
  const mockStats = {
    totalRecharges: 124,
    totalTransferts: 45,
    totalBilleteries: 32,
    totalServices: 18,
    montantTotal: 2850.75
  };

  const mockTransactions = [
    {
      id: 12345,
      type: "Recharge",
      date: "2024-04-05T14:30:00",
      client: "+33612345678",
      montant: 50,
      status: "Success",
    },
    {
      id: 12346,
      type: "Transfert",
      date: "2024-04-05T13:15:00",
      client: "+33623456789",
      montant: 100,
      status: "Success",
    },
    {
      id: 12347,
      type: "Service",
      date: "2024-04-05T11:45:00",
      client: "+33634567890",
      montant: 25.50,
      status: "Pending",
    },
    {
      id: 12348,
      type: "Billeterie",
      date: "2024-04-04T17:20:00",
      client: "+33645678901",
      montant: 80,
      status: "Success",
    },
    {
      id: 12349,
      type: "Recharge",
      date: "2024-04-04T09:10:00",
      client: "+33656789012",
      montant: 20,
      status: "Failed",
    },
  ];

  const transactionColumns = [
    {
      key: "id",
      header: "Réf",
      render: (value: any) => <span className="font-medium text-blue-600">#{value}</span>,
    },
    {
      key: "type",
      header: "Type",
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
          default:
            badgeClass += "bg-gray-100 text-gray-700";
        }
        
        return <span className={badgeClass}>{value}</span>;
      },
    },
    {
      key: "date",
      header: "Date",
      render: (value: any) => formatDate(value),
    },
    {
      key: "client",
      header: "Client",
    },
    {
      key: "montant",
      header: "Montant",
      render: (value: any) => formatCurrency(value),
    },
    {
      key: "status",
      header: "Status",
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
          default:
            badgeClass += "bg-gray-100 text-gray-700";
        }
        
        return <span className={badgeClass}>{value}</span>;
      },
    },
  ];

  const data = stats || mockStats;
  
  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Tableau de bord</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatsCard
          title="Recharges"
          value={data.totalRecharges}
          icon={<CreditCard size={24} />}
          iconColor="text-blue-600"
          iconBackground="bg-blue-100"
        />
        
        <StatsCard
          title="Transferts"
          value={data.totalTransferts}
          icon={<ArrowLeftRight size={24} />}
          iconColor="text-green-600"
          iconBackground="bg-green-100"
        />
        
        <StatsCard
          title="Billeteries"
          value={data.totalBilleteries}
          icon={<TicketCheck size={24} />}
          iconColor="text-purple-600"
          iconBackground="bg-purple-100"
        />
        
        <StatsCard
          title="Services"
          value={data.totalServices}
          icon={<ShoppingCart size={24} />}
          iconColor="text-amber-600"
          iconBackground="bg-amber-100"
        />
      </div>

      <div className="mt-8">
        <h2 className="text-lg font-semibold mb-4">Informations</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <DataDisplay
            title="Solde actuel"
            value={formatCurrency(balance)}
            icon={<CreditCard size={20} />}
            className="h-full"
          />
          
          <DataDisplay
            title="Chiffre d'affaires"
            value={formatCurrency(data.montantTotal)}
            trend={7.2}
            trendLabel="vs. mois dernier"
            className="h-full"
          />
          
          <DataDisplay
            title="Boutique"
            value={authState.user?.nom || ""}
            className="h-full"
          />
        </div>
      </div>

      <div className="mt-8">
        <h2 className="text-lg font-semibold mb-4">Transactions Récentes</h2>
        
        <TableDisplay
          data={recentTransactions || mockTransactions}
          columns={transactionColumns}
          searchable={false}
        />
      </div>
    </div>
  );
};

export default OfficeDashboard;
