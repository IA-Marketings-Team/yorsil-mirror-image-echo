
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";

const Recharge: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Mock data for demonstration
  const rechargesData = [
    { 
      id: 1, 
      date: "01-04-2025 | 13:04", 
      boutique: "AT SERVICES", 
      montant: "9.9 €", 
      operateur: "SYMACOM", 
      description: "SYMA Forfait bloqué 9,90 €", 
      quantite: 1, 
      process_state: "SOLD" 
    },
    { 
      id: 2, 
      date: "01-04-2025 | 17:04", 
      boutique: "AT SERVICES", 
      montant: "9.9 €", 
      operateur: "SYMACOM", 
      description: "SYMA Forfait bloqué 9,90 €", 
      quantite: 1, 
      process_state: "SOLD" 
    },
    { 
      id: 3, 
      date: "02-04-2025 | 09:04", 
      boutique: "AT SERVICES", 
      montant: "5 €", 
      operateur: "ORANGE", 
      description: "Orange Max 5€", 
      quantite: 1, 
      process_state: "SOLD" 
    },
  ];

  const columns = [
    { header: "Date", accessor: "date", sortable: true },
    { header: "Boutique", accessor: "boutique", sortable: true },
    { header: "Montant", accessor: "montant", sortable: true },
    { header: "Operateur", accessor: "operateur", sortable: true },
    { header: "Description", accessor: "description", sortable: true },
    { header: "Quantité", accessor: "quantite", sortable: true },
    { header: "Process state", accessor: "process_state", sortable: true },
  ];

  return (
    <MainLayout>
      <div className="p-6">
        <PageHeader
          title="Journal des recharges"
          breadcrumbs={[
            { label: "Recharge" },
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
            actionItems={[
              { 
                icon: <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="lucide lucide-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>, 
                onClick: (row) => console.log("Download", row) 
              }
            ]}
            showActions={true}
          />
        </div>
      </div>
    </MainLayout>
  );
};

export default Recharge;
