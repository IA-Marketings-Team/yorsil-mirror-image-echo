
import { Toaster } from "@/components/ui/toaster";
import { Toaster as Sonner } from "@/components/ui/sonner";
import { TooltipProvider } from "@/components/ui/tooltip";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { BrowserRouter, Routes, Route } from "react-router-dom";

import Index from "./pages/Index";
import NotFound from "./pages/NotFound";
import ServiceFees from "./pages/journal/ServiceFees";
import AddServiceFee from "./pages/journal/AddServiceFee";
import ShopRecharge from "./pages/journal/ShopRecharge";
import Debit from "./pages/journal/Debit";
import Deposit from "./pages/journal/Deposit";
import CommercialTransactions from "./pages/journal/CommercialTransactions";
import TransferJournal from "./pages/journal/TransferJournal";
import DiaspoTransfer from "./pages/journal/DiaspoTransfer";
import Reservations from "./pages/journal/Reservations";
import Recharge from "./pages/journal/Recharge";
import InvoicePayment from "./pages/journal/InvoicePayment";
import JournalPages from "./pages/journal";

const queryClient = new QueryClient();

const App = () => (
  <QueryClientProvider client={queryClient}>
    <TooltipProvider>
      <Toaster />
      <Sonner />
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Index />} />
          
          {/* Journal Routes */}
          <Route path="/journal" element={<JournalPages />}>
            <Route path="frais-services" element={<ServiceFees />} />
            <Route path="ajouter-frais" element={<AddServiceFee />} />
            <Route path="recharge-boutique" element={<ShopRecharge />} />
            <Route path="debit" element={<Debit />} />
            <Route path="depot" element={<Deposit />} />
            <Route path="gestes-commerciales" element={<CommercialTransactions />} />
            <Route path="transferts" element={<TransferJournal />} />
            <Route path="diaspo-transfert" element={<DiaspoTransfer />} />
            <Route path="reservations" element={<Reservations />} />
            <Route path="recharges" element={<Recharge />} />
            <Route path="paiement-facture" element={<InvoicePayment />} />
          </Route>

          {/* Direct routes without /journal prefix */}
          <Route path="/frais-services" element={<ServiceFees />} />
          <Route path="/ajouter-frais" element={<AddServiceFee />} />
          <Route path="/recharge-boutique" element={<ShopRecharge />} />
          <Route path="/debit" element={<Debit />} />
          <Route path="/depot" element={<Deposit />} />
          <Route path="/gestes-commerciales" element={<CommercialTransactions />} />
          <Route path="/transferts" element={<TransferJournal />} />
          <Route path="/diaspo-transfert" element={<DiaspoTransfer />} />
          <Route path="/reservations" element={<Reservations />} />
          <Route path="/recharges" element={<Recharge />} />
          <Route path="/paiement-facture" element={<InvoicePayment />} />
          
          {/* ADD ALL CUSTOM ROUTES ABOVE THE CATCH-ALL "*" ROUTE */}
          <Route path="*" element={<NotFound />} />
        </Routes>
      </BrowserRouter>
    </TooltipProvider>
  </QueryClientProvider>
);

export default App;
