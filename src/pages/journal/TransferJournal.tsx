
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";

const TransferJournal: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Mock data for demonstration
  const transfersData = [
    { 
      id: 1, 
      date: "05-04-2025", 
      boutique: "AT SERVICES", 
      numero: "+212674162877", 
      montant: "5€", 
      commission: "5%", 
      fx: "9.18", 
      operateur: "Orange Morocco", 
      pays: "Maroc", 
      type: "api", 
      status: "success" 
    },
    { 
      id: 2, 
      date: "05-04-2025", 
      boutique: "AT SERVICES", 
      numero: "+212678612638", 
      montant: "1.15€", 
      commission: "2%", 
      fx: "8.73", 
      operateur: "Maroc Telecom Morocco", 
      pays: "Maroc", 
      type: "api", 
      status: "success" 
    },
    { 
      id: 3, 
      date: "31-03-2025", 
      boutique: "AT SERVICES", 
      numero: "+212678612638", 
      montant: "1.15€", 
      commission: "2%", 
      fx: "8.67", 
      operateur: "Maroc Telecom Morocco", 
      pays: "Maroc", 
      type: "api", 
      status: "success" 
    }
  ];

  const columns = [
    { header: "Date", accessor: "date", sortable: true },
    { header: "Boutique", accessor: "boutique", sortable: true },
    { header: "Numéro", accessor: "numero", sortable: true },
    { header: "Montant", accessor: "montant", sortable: true },
    { header: "Commission", accessor: "commission", sortable: true },
    { header: "Fx", accessor: "fx", sortable: true },
    { header: "Opérateur", accessor: "operateur", sortable: true },
    { header: "Pays", accessor: "pays", sortable: true },
    { header: "Type", accessor: "type", sortable: true },
    { 
      header: "Status", 
      accessor: "status", 
      render: (value) => (
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-signal-high text-teal-500"><path d="M2 20h.01"/><path d="M7 20v-4"/><path d="M12 20v-8"/><path d="M17 20v-12"/></svg>
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
                icon: <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>,
                onClick: (row) => console.log("Validate", row)
              }
            ]}
          />
        </div>
      </div>
    </MainLayout>
  );
};

export default TransferJournal;
