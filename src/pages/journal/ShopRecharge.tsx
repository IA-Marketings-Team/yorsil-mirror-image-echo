
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";
import StatusBadge from "@/components/common/StatusBadge";
import { Eye } from "lucide-react";

const ShopRecharge: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Mock data for demonstration
  const rechargesData = [
    { 
      id: 1, 
      date: "29/03/2025", 
      admin: "--", 
      boutique: "AT SERVICES", 
      ref: "CB008", 
      montant: "493.11 €", 
      type: "Cartes CB", 
      percepteur: "Aucun", 
      status: "success", 
    },
    { 
      id: 2, 
      date: "07/03/2025", 
      admin: "--", 
      boutique: "AT SERVICES", 
      ref: "CB007", 
      montant: "300 €", 
      type: "Cartes CB", 
      percepteur: "Aucun", 
      status: "success", 
    },
    { 
      id: 3, 
      date: "07/03/2025", 
      admin: "TARIK", 
      boutique: "AT SERVICES", 
      ref: "ESP009", 
      montant: "500 €", 
      type: "Espèces", 
      percepteur: "Aucun", 
      status: "success", 
    },
  ];

  const columns = [
    { header: "Date", accessor: "date", sortable: true },
    { header: "Admin", accessor: "admin", sortable: true },
    { header: "Boutique", accessor: "boutique", sortable: true },
    { header: "Ref", accessor: "ref", sortable: true },
    { header: "Montant", accessor: "montant", sortable: true },
    { header: "Type", accessor: "type", sortable: true },
    { header: "Percepteur", accessor: "percepteur", sortable: true },
    { 
      header: "Status", 
      accessor: "status", 
      sortable: true,
      render: (value: string) => (
        <StatusBadge status={value as "success" | "pending" | "error"} />
      )
    },
    { 
      header: "Justificatif", 
      accessor: "justificatif",
      render: () => (
        <div className="flex justify-center">
          <Eye size={20} />
        </div>
      )
    },
  ];

  return (
    <MainLayout>
      <div className="p-6">
        <PageHeader
          title="Liste rechargements par boutique"
          breadcrumbs={[
            { label: "Rechargement" },
            { label: "Liste" },
          ]}
        />

        <div className="bg-white p-6 rounded-lg shadow">
          <TableControls
            totalElements={rechargesData.length}
            elementsPerPage={10}
            currentPage={currentPage}
            onPageChange={setCurrentPage}
            onSearch={setSearchQuery}
          />

          <DataTable 
            columns={columns} 
            data={rechargesData}
            showActions={true}
            actionItems={[
              {
                icon: <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>,
                onClick: (row) => console.log("Validate", row)
              },
              {
                icon: <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>,
                onClick: (row) => console.log("Delete", row)
              }
            ]}
          />
        </div>
      </div>
    </MainLayout>
  );
};

export default ShopRecharge;
