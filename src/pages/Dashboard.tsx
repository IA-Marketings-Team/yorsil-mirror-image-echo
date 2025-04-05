
import { useState } from "react";
import Sidebar from "@/components/layout/Sidebar";
import StatCard from "@/components/dashboard/StatCard";
import BusinessCard from "@/components/dashboard/BusinessCard";
import InvoiceModal from "@/components/modals/InvoiceModal";
import { Button } from "@/components/ui/button";
import { Bell } from "lucide-react";

const Dashboard = () => {
  const [showInvoiceModal, setShowInvoiceModal] = useState(false);

  return (
    <div className="flex h-screen bg-gray-50">
      <Sidebar />
      
      <div className="flex-1 overflow-auto">
        <header className="bg-white p-4 shadow flex justify-between items-center">
          <div></div>
          <div className="flex items-center space-x-4">
            <div className="relative">
              <Bell size={20} />
              <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">2</span>
            </div>
            <div className="flex items-center space-x-2">
              <span className="text-gray-700">Tarik</span>
              <div className="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white">T</div>
            </div>
          </div>
        </header>
        
        <main className="p-6">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <StatCard 
              title="CHIFFRE D'AFFAIRES" 
              value="1,00 €" 
              backgroundColor="bg-yorsil-blue" 
              textColor="text-yorsil-text"
            />
            
            <StatCard 
              title="COMMISSION" 
              value="44,32 €" 
              backgroundColor="bg-yorsil-blue" 
              textColor="text-yorsil-text"
            />
            
            <StatCard 
              title="PARTENAIRES" 
              value="29" 
              backgroundColor="bg-yorsil-orange" 
              textColor="text-amber-600"
            />
          </div>
          
          <div className="flex justify-end mb-6">
            <Button 
              onClick={() => setShowInvoiceModal(true)}
              className="bg-yorsil-accent hover:bg-yorsil-accent/90 text-white"
            >
              Facturation Services
            </Button>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <BusinessCard title="ALEDA" value="207,58 €" />
            <BusinessCard title="RELOADLY" value="85,66 €" />
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <BusinessCard title="Chiffre d'affaires FLIXBUS" value="707,20 €" />
            <BusinessCard title="Chiffre d'affaires CARTE DE RECHARGE" value="974,09 €" />
            <BusinessCard title="Chiffre d'affaires TRANSFERT DE CREDIT" value="32,00 €" />
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <BusinessCard title="Chiffre d'affaires Du Jour" value="155,65 €" />
            <BusinessCard title="Chiffre d'affaires Du Mois" value="329,91 €" />
            <BusinessCard title="Chiffre d'affaires Totale" value="1713,29 €" />
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <BusinessCard title="Commission FLIXBUS" value="19,36 €" />
            <BusinessCard title="Commission CARTE DE RECHARGE" value="24,56 €" />
            <BusinessCard title="Commission TRANSFERT DE CREDIT" value="0,40 €" />
          </div>
        </main>
      </div>
      
      {showInvoiceModal && (
        <InvoiceModal 
          isOpen={showInvoiceModal} 
          onClose={() => setShowInvoiceModal(false)} 
        />
      )}
    </div>
  );
};

export default Dashboard;
