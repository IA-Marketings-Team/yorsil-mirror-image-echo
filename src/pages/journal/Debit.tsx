
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";

const Debit: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Mock data for demonstration
  const debitsData = [
    { 
      id: 1, 
      admin: "",
      boutique: "AT SERVICES", 
      montant: "4", 
      date: "05-04-2025", 
      note: "Carte e-recharge 5 € SMS (Max) de 5.00 €", 
    },
    { 
      id: 2, 
      admin: "",
      boutique: "AT SERVICES", 
      montant: "8", 
      date: "05-04-2025", 
      note: "Carte e-recharge 10€ illimité (Max) de 10.00 €", 
    },
    { 
      id: 3, 
      admin: "",
      boutique: "AT SERVICES", 
      montant: "8", 
      date: "05-04-2025", 
      note: "Carte e-recharge 10€ illimité (Max) de 10.00 €", 
    },
  ];

  const columns = [
    { header: "Nom admin", accessor: "admin", sortable: true },
    { header: "Boutique", accessor: "boutique", sortable: true },
    { header: "Montant", accessor: "montant", sortable: true },
    { header: "Date", accessor: "date", sortable: true },
    { header: "Note", accessor: "note", sortable: true },
  ];

  return (
    <MainLayout>
      <div className="p-6">
        <PageHeader
          title="Liste débits par boutique"
          breadcrumbs={[
            { label: "Débit" },
            { label: "Liste" },
          ]}
        />

        <div className="bg-white p-6 rounded-lg shadow">
          <TableControls
            totalElements={debitsData.length}
            elementsPerPage={10}
            currentPage={currentPage}
            onPageChange={setCurrentPage}
            onSearch={setSearchQuery}
          />

          <DataTable 
            columns={columns} 
            data={debitsData} 
          />
        </div>
      </div>
    </MainLayout>
  );
};

export default Debit;
