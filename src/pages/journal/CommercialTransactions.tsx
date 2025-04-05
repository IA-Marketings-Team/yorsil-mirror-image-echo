
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";

const CommercialTransactions: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Empty data for demonstration
  const transactionsData: any[] = [];

  const columns = [
    { header: "Date", accessor: "date", sortable: true },
    { header: "Admin", accessor: "admin", sortable: true },
    { header: "Boutique", accessor: "boutique", sortable: true },
    { header: "Montant", accessor: "montant", sortable: true },
    { header: "Motif", accessor: "motif", sortable: true },
  ];

  return (
    <MainLayout>
      <div className="p-6">
        <PageHeader
          title="Liste des Gestes commerciales par boutique"
          breadcrumbs={[
            { label: "Geste Commerciale" },
            { label: "Liste" },
          ]}
        />

        <div className="bg-white p-6 rounded-lg shadow">
          <TableControls
            totalElements={transactionsData.length}
            elementsPerPage={10}
            currentPage={currentPage}
            onPageChange={setCurrentPage}
            onSearch={setSearchQuery}
          />

          <DataTable 
            columns={columns} 
            data={transactionsData} 
          />
        </div>
      </div>
    </MainLayout>
  );
};

export default CommercialTransactions;
