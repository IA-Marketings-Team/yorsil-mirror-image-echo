
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";

const InvoicePayment: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Empty data for demonstration
  const paymentsData: any[] = [];

  const columns = [
    { header: "Date", accessor: "date", sortable: true },
    { header: "Boutique", accessor: "boutique", sortable: true },
    { header: "RefTxFatourati", accessor: "refTxFatourati", sortable: true },
    { header: "MontantTTC", accessor: "montantTTC", sortable: true },
    { header: "Montant(€)", accessor: "montantEuro", sortable: true },
  ];

  return (
    <MainLayout>
      <div className="p-6">
        <PageHeader
          title="Journal de paiement de facture"
          breadcrumbs={[
            { label: "Paiement de facture" },
            { label: "Liste" },
          ]}
        />

        <div className="bg-white p-6 rounded-lg shadow">
          <TableControls
            totalElements={paymentsData.length}
            elementsPerPage={10}
            currentPage={currentPage}
            onPageChange={setCurrentPage}
            onSearch={setSearchQuery}
          />

          <DataTable 
            columns={columns} 
            data={paymentsData} 
          />
        </div>
      </div>
    </MainLayout>
  );
};

export default InvoicePayment;
