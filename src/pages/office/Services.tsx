
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { api } from "@/services/api";
import { Search, User, FileText, ChevronRight, ShoppingCart } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { formatCurrency } from "@/lib/utils";
import { toast } from "react-toastify";

const OfficeServices = () => {
  const [identifier, setIdentifier] = useState("");
  const [searchType, setSearchType] = useState<"customer" | "invoice">("customer");
  const [isSearching, setIsSearching] = useState(false);
  const [searchResults, setSearchResults] = useState<any[]>([]);
  const [selectedInvoice, setSelectedInvoice] = useState<any>(null);
  const [isLoading, setIsLoading] = useState(false);

  // Mock search results for demonstration
  const mockCustomerResults = [
    {
      id: 1,
      name: "Jean Dupont",
      customerNumber: "C12345",
      unpaidInvoices: 2,
      totalDue: 89.50,
    },
    {
      id: 2,
      name: "Marie Martin",
      customerNumber: "C12346",
      unpaidInvoices: 1,
      totalDue: 45.75,
    }
  ];

  const mockInvoiceResults = [
    {
      id: 101,
      invoiceNumber: "F20240401-101",
      date: "2024-04-01",
      amount: 45.75,
      customer: "Marie Martin",
      customerNumber: "C12346",
      status: "unpaid",
    },
    {
      id: 102,
      invoiceNumber: "F20240325-102",
      date: "2024-03-25",
      amount: 65.20,
      customer: "Jean Dupont",
      customerNumber: "C12345",
      status: "unpaid",
    },
    {
      id: 103,
      invoiceNumber: "F20240320-103",
      date: "2024-03-20",
      amount: 24.30,
      customer: "Jean Dupont",
      customerNumber: "C12345",
      status: "unpaid",
    }
  ];

  const handleSearch = () => {
    if (!identifier) {
      toast.error("Veuillez saisir un identifiant");
      return;
    }

    setIsSearching(true);
    setSearchResults([]);
    setSelectedInvoice(null);

    // In a real app, this would call the API
    // api.get(`/services/search?type=${searchType}&identifier=${identifier}`)
    //   .then(response => {
    //     setSearchResults(response.data);
    //     setIsSearching(false);
    //   })
    //   .catch(error => {
    //     toast.error("Erreur lors de la recherche");
    //     setIsSearching(false);
    //   });

    // Simulate API call
    setTimeout(() => {
      if (searchType === "customer") {
        setSearchResults(mockCustomerResults.filter(c => 
          c.customerNumber.includes(identifier) || 
          c.name.toLowerCase().includes(identifier.toLowerCase())
        ));
      } else {
        setSearchResults(mockInvoiceResults.filter(i => 
          i.invoiceNumber.includes(identifier)
        ));
      }
      setIsSearching(false);
    }, 1000);
  };

  const handleSelectCustomer = (customer: any) => {
    // Get all unpaid invoices for this customer
    setSearchResults(mockInvoiceResults.filter(i => i.customerNumber === customer.customerNumber));
  };

  const handleSelectInvoice = (invoice: any) => {
    setSelectedInvoice(invoice);
  };

  const handlePayInvoice = () => {
    setIsLoading(true);

    // In a real app, this would call the API
    // api.post('/services/payment', {
    //   invoiceId: selectedInvoice.id
    // })
    //   .then(response => {
    //     toast.success("Facture payée avec succès");
    //     setSelectedInvoice(null);
    //     setSearchResults(searchResults.filter(inv => inv.id !== selectedInvoice.id));
    //     setIsLoading(false);
    //   })
    //   .catch(error => {
    //     toast.error("Erreur lors du paiement");
    //     setIsLoading(false);
    //   });

    // Simulate API call
    setTimeout(() => {
      toast.success("Facture payée avec succès");
      setSelectedInvoice(null);
      setSearchResults(searchResults.filter(inv => inv.id !== selectedInvoice.id));
      setIsLoading(false);
    }, 1500);
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Services de Paiement</h1>
      </div>

      <div className="bg-white rounded-lg shadow p-6">
        <div className="space-y-4">
          <h2 className="text-lg font-semibold">Rechercher une facture</h2>
          
          <div className="flex space-x-4">
            <div className="flex items-center space-x-2">
              <input
                type="radio"
                id="searchCustomer"
                checked={searchType === "customer"}
                onChange={() => setSearchType("customer")}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor="searchCustomer" className="text-sm font-medium text-gray-700">
                Par client
              </label>
            </div>
            
            <div className="flex items-center space-x-2">
              <input
                type="radio"
                id="searchInvoice"
                checked={searchType === "invoice"}
                onChange={() => setSearchType("invoice")}
                className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label htmlFor="searchInvoice" className="text-sm font-medium text-gray-700">
                Par numéro de facture
              </label>
            </div>
          </div>
          
          <div className="flex space-x-4">
            <div className="relative flex-1">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                {searchType === "customer" ? (
                  <User size={18} className="text-gray-400" />
                ) : (
                  <FileText size={18} className="text-gray-400" />
                )}
              </div>
              <Input
                type="text"
                value={identifier}
                onChange={(e) => setIdentifier(e.target.value)}
                className="pl-10"
                placeholder={
                  searchType === "customer" 
                    ? "N° client ou nom du client" 
                    : "N° de facture"
                }
              />
            </div>
            
            <Button 
              onClick={handleSearch}
              disabled={isSearching}
              className="flex items-center gap-2"
            >
              <Search size={18} />
              {isSearching ? "Recherche..." : "Rechercher"}
            </Button>
          </div>
        </div>
      </div>

      {searchResults.length > 0 && searchType === "customer" && !selectedInvoice && (
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="p-4 border-b">
            <h2 className="text-lg font-semibold">Résultats de la recherche</h2>
            <p className="text-sm text-gray-500">{searchResults.length} clients trouvés</p>
          </div>
          
          <div className="divide-y">
            {searchResults.map((customer) => (
              <div
                key={customer.id}
                className="p-4 hover:bg-gray-50 cursor-pointer flex items-center justify-between"
                onClick={() => handleSelectCustomer(customer)}
              >
                <div>
                  <h3 className="font-medium">{customer.name}</h3>
                  <p className="text-sm text-gray-500">N° client: {customer.customerNumber}</p>
                  <p className="text-sm text-gray-500">{customer.unpaidInvoices} factures impayées</p>
                </div>
                
                <div className="flex items-center">
                  <div className="text-right mr-2">
                    <p className="text-lg font-semibold">{formatCurrency(customer.totalDue)}</p>
                    <p className="text-sm text-gray-500">Total dû</p>
                  </div>
                  <ChevronRight size={20} className="text-gray-400" />
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {searchResults.length > 0 && (searchType === "invoice" || selectedInvoice) && (
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="p-4 border-b">
            <h2 className="text-lg font-semibold">Factures</h2>
            <p className="text-sm text-gray-500">{searchResults.length} factures trouvées</p>
          </div>
          
          <div className="divide-y">
            {searchResults.map((invoice) => (
              <div
                key={invoice.id}
                className={`p-4 hover:bg-gray-50 cursor-pointer ${
                  selectedInvoice?.id === invoice.id ? "bg-blue-50" : ""
                }`}
                onClick={() => handleSelectInvoice(invoice)}
              >
                <div className="flex flex-col md:flex-row md:items-center justify-between">
                  <div>
                    <h3 className="font-medium">{invoice.invoiceNumber}</h3>
                    <p className="text-sm text-gray-500">Date: {invoice.date}</p>
                    <p className="text-sm text-gray-500">Client: {invoice.customer}</p>
                  </div>
                  
                  <div className="mt-2 md:mt-0 md:text-right">
                    <p className="text-xl font-semibold">{formatCurrency(invoice.amount)}</p>
                    <span
                      className="inline-flex items-center rounded-md bg-red-100 px-2 py-1 text-xs font-medium text-red-700"
                    >
                      Non payée
                    </span>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {selectedInvoice && (
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold mb-4">Paiement de facture</h2>
          
          <div className="mb-6 p-4 bg-blue-50 rounded-md border border-blue-200">
            <div className="grid grid-cols-2 gap-4">
              <div>
                <h3 className="text-sm font-medium text-gray-500">N° Facture</h3>
                <p className="font-medium">{selectedInvoice.invoiceNumber}</p>
              </div>
              
              <div>
                <h3 className="text-sm font-medium text-gray-500">Date</h3>
                <p className="font-medium">{selectedInvoice.date}</p>
              </div>
              
              <div>
                <h3 className="text-sm font-medium text-gray-500">Client</h3>
                <p className="font-medium">{selectedInvoice.customer}</p>
              </div>
              
              <div>
                <h3 className="text-sm font-medium text-gray-500">N° Client</h3>
                <p className="font-medium">{selectedInvoice.customerNumber}</p>
              </div>
              
              <div className="col-span-2">
                <h3 className="text-sm font-medium text-gray-500">Montant</h3>
                <p className="font-medium text-xl">{formatCurrency(selectedInvoice.amount)}</p>
              </div>
            </div>
          </div>
          
          <div className="pt-4 flex justify-between">
            <Button
              variant="outline"
              onClick={() => setSelectedInvoice(null)}
            >
              Annuler
            </Button>
            
            <Button
              onClick={handlePayInvoice}
              disabled={isLoading}
              className="flex items-center gap-2"
            >
              <ShoppingCart size={18} />
              {isLoading ? "Paiement en cours..." : "Payer la facture"}
            </Button>
          </div>
        </div>
      )}
    </div>
  );
};

export default OfficeServices;
