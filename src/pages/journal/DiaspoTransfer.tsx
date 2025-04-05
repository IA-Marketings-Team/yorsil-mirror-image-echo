
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";
import StatusBadge from "@/components/common/StatusBadge";
import { Button } from "@/components/ui/button";

const DiaspoTransfer: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Mock data for demonstration
  const transfersData = [
    { 
      id: 1, 
      date: "02-12-2024", 
      boutique: "ATS SERVICES", 
      numero: "+212", 
      montant: "4.00 €", 
      operateur: "Maroc Telecom Morocco", 
      pays: "Maroc", 
      status: "validated", 
    },
    { 
      id: 2, 
      date: "02-12-2024", 
      boutique: "Test", 
      numero: "+212651804111", 
      montant: "4.00 €", 
      operateur: "Maroc Telecom Morocco", 
      pays: "Maroc", 
      status: "validated", 
    },
    { 
      id: 3, 
      date: "25-11-2024", 
      boutique: "Test", 
      numero: "+212651804111", 
      montant: "4.00 €", 
      operateur: "Maroc Telecom Morocco", 
      pays: "Maroc", 
      status: "pending", 
    },
    { 
      id: 4, 
      date: "25-11-2024", 
      boutique: "Test", 
      numero: "+212651804111", 
      montant: "4.00 €", 
      operateur: "Maroc Telecom Morocco", 
      pays: "Maroc", 
      status: "pending", 
    },
  ];

  const statusColors: {[key: string]: "success" | "pending" | "error"} = {
    validated: "success",
    pending: "pending",
  };

  const columns = [
    { header: "Date", accessor: "date", sortable: true },
    { header: "Boutique", accessor: "boutique", sortable: true },
    { header: "Numéro", accessor: "numero", sortable: true },
    { header: "Montant", accessor: "montant", sortable: true },
    { header: "Opérateur", accessor: "operateur", sortable: true },
    { header: "Pays", accessor: "pays", sortable: true },
    { 
      header: "Status", 
      accessor: "status", 
      render: (value: string) => (
        <div className="flex justify-center">
          <StatusBadge 
            status={statusColors[value] || "pending"} 
            text={value === "validated" ? "Validé" : "En attente"} 
          />
        </div>
      )
    },
  ];

  return (
    <MainLayout>
      <div className="p-6">
        <PageHeader
          title="Journal des transferts de crédit"
          breadcrumbs={[
            { label: "Transfert de crédit" },
            { label: "Liste" },
          ]}
        />

        <div className="bg-white p-6 rounded-lg shadow">
          <TableControls
            totalElements={transfersData.length}
            elementsPerPage={10}
            currentPage={currentPage}
            onPageChange={setCurrentPage}
            onSearch={setSearchQuery}
          />

          <DataTable 
            columns={columns} 
            data={transfersData}
            showActions={true}
            actionItems={[
              {
                icon: row => row.status === "validated" ? (
                  <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                ) : (
                  <Button className="bg-teal-600 hover:bg-teal-700 text-white">
                    Valider
                  </Button>
                ),
                onClick: (row) => console.log("Validate", row)
              }
            ]}
          />

          <div className="mt-4 text-sm text-gray-600">
            Affichage d'éléments 1 à 5 sur 5 éléments
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

export default DiaspoTransfer;
