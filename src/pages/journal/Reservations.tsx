
import React, { useState } from "react";
import MainLayout from "@/components/layout/MainLayout";
import PageHeader from "@/components/common/PageHeader";
import DataTable from "@/components/common/DataTable";
import TableControls from "@/components/common/TableControls";

const Reservations: React.FC = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);

  // Mock data for demonstration
  const reservationsData = [
    { 
      id: 1,
      date_reservation: "02-12-2024 | 19:12", 
      date_depart: "02-12-2024 | 19:12", 
      date_arriver: "02-12-2024 | 23:12", 
      station_depart: "Avignon (Le Pontet)", 
      station_arriver: "Chalon-sur-Saône", 
      montant: "23.48 €", 
      passagers: "1 adulte" 
    },
    { 
      id: 2,
      date_reservation: "04-04-2025 | 16:04", 
      date_depart: "04-04-2025 | 16:04", 
      date_arriver: "04-04-2025 | 22:04", 
      station_depart: "Lyon (Perrache)", 
      station_arriver: "Paris (Bercy Seine)", 
      montant: "36.98 €", 
      passagers: "1 adulte" 
    },
    { 
      id: 3,
      date_reservation: "04-04-2025 | 16:04", 
      date_depart: "04-04-2025 | 16:04", 
      date_arriver: "04-04-2025 | 22:04", 
      station_depart: "Lyon (Perrache)", 
      station_arriver: "Paris (Bercy Seine)", 
      montant: "37.98 €", 
      passagers: "1 adulte" 
    }
  ];

  const columns = [
    { header: "Date de réservation", accessor: "date_reservation", sortable: true },
    { header: "Date de départ", accessor: "date_depart", sortable: true },
    { header: "Date d'arriver", accessor: "date_arriver", sortable: true },
    { header: "Station de départ", accessor: "station_depart", sortable: true },
    { header: "Station d'arriver", accessor: "station_arriver", sortable: true },
    { header: "Montant", accessor: "montant", sortable: true },
    { header: "Passagers", accessor: "passagers", sortable: true },
  ];

  return (
    <MainLayout>
      <div className="p-6">
        <PageHeader
          title="Journal des reservations"
          breadcrumbs={[
            { label: "Reservation" },
            { label: "Liste" },
          ]}
        />

        <div className="bg-white p-6 rounded-lg shadow">
          <TableControls
            totalElements={reservationsData.length}
            elementsPerPage={10}
            currentPage={currentPage}
            onPageChange={setCurrentPage}
            onSearch={setSearchQuery}
          />

          <DataTable 
            columns={columns} 
            data={reservationsData} 
          />
        </div>
      </div>
    </MainLayout>
  );
};

export default Reservations;
