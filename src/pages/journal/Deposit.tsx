
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";
import StatusBadge from "@/components/common/StatusBadge";
import { Eye } from "lucide-react";

const Deposit: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Mock data for demonstration
  const depositsData = [
    { 
      id: 1, 
      date: "25-11-2024", 
      percepteur: "Test", 
      montant: "200 €", 
      note: "TEST", 
      status: "success", 
    },
  ];

  const columns = [
    { header: "Date", accessor: "date", sortable: true },
    { header: "Percepteur", accessor: "percepteur", sortable: true },
    { header: "Montant", accessor: "montant", sortable: true },
    { header: "Note", accessor: "note", sortable: true },
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
          title="Liste Dépôt"
          breadcrumbs={[
            { label: "Dépôt" },
            { label: "Liste" },
          ]}
        />

        <div className="bg-white p-6 rounded-lg shadow">
          <TableControls
            totalElements={depositsData.length}
            elementsPerPage={10}
            currentPage={currentPage}
            onPageChange={setCurrentPage}
            onSearch={setSearchQuery}
          />

          <DataTable 
            columns={columns} 
            data={depositsData}
            showActions={true}
            actionItems={[
              { 
                icon: <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-check"><polyline points="20 6 9 17 4 12"/></svg>, 
                onClick: (row) => console.log("Check", row) 
              }
            ]}
          />

          <div className="mt-4 text-sm text-gray-600">
            Affichage d'éléments 1 à 1 sur 1 éléments
          </div>

          <div className="mt-4 flex justify-center">
            <button className="px-3 py-1 border border-r-0 rounded-l-md">&laquo;</button>
            <button className="px-3 py-1 border bg-blue-500 text-white">1</button>
            <button className="px-3 py-1 border border-l-0 rounded-r-md">&raquo;</button>
          </div>
        </div>
      </div>
    </MainLayout>
  );
};

export default Deposit;
