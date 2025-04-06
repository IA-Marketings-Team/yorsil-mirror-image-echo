
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";
import StatusBadge from "@/components/common/StatusBadge";
import { Button } from "@/components/ui/button";
import { PlusCircle, FileText, Eye } from "lucide-react";
import { format } from "date-fns";

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
    { 
      id: 4, 
      date: "02/03/2025", 
      admin: "TARIK", 
      boutique: "AT SERVICES", 
      ref: "ESP008", 
      montant: "300 €", 
      type: "Espèces", 
      percepteur: "Aucun", 
      status: "success", 
    },
    { 
      id: 5, 
      date: "02/03/2025", 
      admin: "--", 
      boutique: "AT SERVICES", 
      ref: "CB006", 
      montant: "493.11 €", 
      type: "Cartes CB", 
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
          <Eye size={20} className="text-gray-600 hover:text-blue-600 cursor-pointer" />
        </div>
      )
    },
  ];

  const handleAddRecharge = () => {
    console.log("Ajouter un rechargement");
  };

  const currentDate = format(new Date(), "dd/MM/yyyy");

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
          <div className="flex justify-between items-center mb-6">
            <Button 
              onClick={handleAddRecharge}
              className="bg-teal-600 hover:bg-teal-700 text-white flex items-center"
            >
              <PlusCircle size={18} className="mr-2" />
              Ajouter un rechargement
            </Button>
            
            <div className="flex items-center">
              <span className="mr-2 text-gray-600">Date:</span>
              <span className="font-medium">{currentDate}</span>
              <Button variant="outline" className="ml-4 flex items-center">
                <FileText size={18} className="mr-2" />
                Export PDF
              </Button>
            </div>
          </div>

          <TableControls
            totalElements={rechargesData.length}
            elementsPerPage={10}
            currentPage={currentPage}
            onPageChange={setCurrentPage}
            onSearch={setSearchQuery}
          />

          <div className="mt-6">
            <DataTable 
              columns={columns} 
              data={rechargesData}
              showActions={true}
              actionItems={[
                {
                  icon: <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="white" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 6 9 17l-5-5"/></svg>,
                  onClick: (row) => console.log("Validate", row)
                },
                {
                  icon: <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>,
                  onClick: (row) => console.log("Delete", row)
                }
              ]}
            />
          </div>
          
          <div className="mt-6 flex justify-between items-center text-sm text-gray-600">
            <div>
              Affichage d'éléments 1 à {Math.min(rechargesData.length, 10)} sur {rechargesData.length} éléments
            </div>
            
            <div className="flex items-center">
              <button className="px-3 py-1 border rounded-l-md">&laquo;</button>
              <button className="px-3 py-1 border-t border-b bg-blue-500 text-white">1</button>
              <button className="px-3 py-1 border rounded-r-md">&raquo;</button>
            </div>
          </div>
        </div>
      </div>
    </MainLayout>
  );
};

export default ShopRecharge;
