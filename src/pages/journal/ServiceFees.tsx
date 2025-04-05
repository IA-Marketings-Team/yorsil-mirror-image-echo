
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Plus, Edit } from "lucide-react";
import { Link } from "react-router-dom";

const ServiceFees: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Mock data for demonstration
  const serviceFeesData = [
    { id: 1, boutique: "TEST", service: "DiaspoTransfert", pourcentage: "3 %" },
  ];

  const columns = [
    { header: "Boutique", accessor: "boutique", sortable: true },
    { header: "Services", accessor: "service", sortable: true },
    { header: "Pourcentage", accessor: "pourcentage", sortable: true },
  ];

  return (
    <MainLayout>
      <div className="p-6">
        <PageHeader
          title="Liste des frais de services personnalisés"
          breadcrumbs={[
            { label: "Frais de services personnalisés" },
            { label: "Liste" },
          ]}
        />

        <div className="bg-white p-6 rounded-lg shadow">
          <div className="mb-6">
            <Link to="/ajouter-frais">
              <Button className="bg-teal-600 hover:bg-teal-700 text-white">
                <Plus size={18} className="mr-2" />
                Ajouter un frais de service personnalisé
              </Button>
            </Link>
          </div>

          <TableControls
            totalElements={serviceFeesData.length}
            elementsPerPage={10}
            currentPage={currentPage}
            onPageChange={setCurrentPage}
            onSearch={setSearchQuery}
          />

          <DataTable 
            columns={columns} 
            data={serviceFeesData}
            showActions={true}
            actionItems={[
              { 
                icon: <Edit className="text-white" />, 
                onClick: (row) => console.log("Edit", row) 
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

export default ServiceFees;
