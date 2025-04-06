
import { useEffect, useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { CreditCard, Users, TrendingUp, ArrowRightLeft } from "lucide-react";
import StatsCard from "@/components/common/StatsCard";
import { formatCurrency, formatDate } from "@/lib/utils";
import { api } from "@/services/api";
import TableDisplay from "@/components/common/TableDisplay";

const AdminDashboard = () => {
  const [statsData, setStatsData] = useState({
    totalUsers: 0,
    totalBoutiques: 0,
    totalTransactions: 0,
    totalAmount: 0
  });

  // Fetch dashboard stats
  const { data: stats } = useQuery({
    queryKey: ['dashboardStats'],
    queryFn: async () => {
      const response = await api.get('/dashboard/stats');
      return response.data;
    }
  });

  // Fetch recent transactions
  const { data: recentTransactions, isLoading: isLoadingTransactions } = useQuery({
    queryKey: ['recentTransactions'],
    queryFn: async () => {
      const response = await api.get('/dashboard/recent-transactions');
      return response.data;
    }
  });

  useEffect(() => {
    if (stats) {
      setStatsData(stats);
    }
  }, [stats]);

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
      key: "boutique",
      header: "Boutique",
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

  // Mock data for recent transactions when API is not available
  const mockTransactions = [
    {
      id: 12345,
      type: "Recharge",
      date: "2024-04-05T14:30:00",
      boutique: "Boutique Centrale",
      montant: 50,
      status: "Success",
    },
    {
      id: 12346,
      type: "Transfert",
      date: "2024-04-05T13:15:00",
      boutique: "Tabac du Coin",
      montant: 100,
      status: "Success",
    },
    {
      id: 12347,
      type: "Service",
      date: "2024-04-05T11:45:00",
      boutique: "Point Service",
      montant: 25.50,
      status: "Pending",
    },
    {
      id: 12348,
      type: "Billeterie",
      date: "2024-04-04T17:20:00",
      boutique: "Boutique Express",
      montant: 80,
      status: "Success",
    },
    {
      id: 12349,
      type: "Recharge",
      date: "2024-04-04T09:10:00",
      boutique: "Boutique Centrale",
      montant: 20,
      status: "Failed",
    },
  ];

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Tableau de bord</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatsCard
          title="Utilisateurs"
          value={statsData.totalUsers}
          icon={<Users size={24} />}
          iconColor="text-blue-600"
          iconBackground="bg-blue-100"
          trend={5.2}
        />
        
        <StatsCard
          title="Boutiques"
          value={statsData.totalBoutiques}
          icon={<Store size={24} />}
          iconColor="text-green-600"
          iconBackground="bg-green-100"
          trend={3.1}
        />
        
        <StatsCard
          title="Transactions"
          value={statsData.totalTransactions}
          icon={<ArrowRightLeft size={24} />}
          iconColor="text-purple-600"
          iconBackground="bg-purple-100"
          trend={7.4}
        />
        
        <StatsCard
          title="Volume Total"
          value={formatCurrency(statsData.totalAmount)}
          icon={<TrendingUp size={24} />}
          iconColor="text-amber-600"
          iconBackground="bg-amber-100"
          trend={12.5}
        />
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

function Store(props: { size: number }) {
  return (
    <svg
      {...props}
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
    >
      <path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7" />
      <path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8" />
      <path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4" />
      <path d="M2 7h20" />
      <path d="M22 7v3a2 2 0 0 1-2 2v0a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 16 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 12 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 8 12a2.7 2.7 0 0 1-1.59-.63.7.7 0 0 0-.82 0A2.7 2.7 0 0 1 4 12v0a2 2 0 0 1-2-2V7" />
    </svg>
  );
}

export default AdminDashboard;
