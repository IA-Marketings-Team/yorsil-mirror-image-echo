
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { api } from "@/services/api";
import { Check, ChevronDown, Phone, X } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { formatCurrency } from "@/lib/utils";
import { toast } from "react-toastify";

const OfficeRecharge = () => {
  const [selectedOperator, setSelectedOperator] = useState<any>(null);
  const [phoneNumber, setPhoneNumber] = useState("");
  const [amount, setAmount] = useState("");
  const [isOperatorMenuOpen, setIsOperatorMenuOpen] = useState(false);
  const [isProductMenuOpen, setIsProductMenuOpen] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState<any>(null);
  const [isLoading, setIsLoading] = useState(false);

  // Fetch operators list
  const { data: operators } = useQuery({
    queryKey: ['operators'],
    queryFn: async () => {
      const response = await api.get('/operators');
      return response.data;
    }
  });

  // Fetch products for selected operator
  const { data: products } = useQuery({
    queryKey: ['products', selectedOperator?.id],
    queryFn: async () => {
      if (!selectedOperator) return [];
      const response = await api.get(`/operators/${selectedOperator.id}/products`);
      return response.data;
    },
    enabled: !!selectedOperator
  });

  // Mock data for when API is not available
  const mockOperators = [
    { id: 1, nom: "Orange", logo: "orange_logo.png", type: "Mobile" },
    { id: 2, nom: "SFR", logo: "sfr_logo.png", type: "Mobile" },
    { id: 3, nom: "Bouygues", logo: "bouygues_logo.png", type: "Mobile" },
    { id: 4, nom: "Free", logo: "free_logo.png", type: "Mobile" }
  ];

  const mockProducts = [
    { id: 1, nom: "Recharge 5€", prixVente: 5 },
    { id: 2, nom: "Recharge 10€", prixVente: 10 },
    { id: 3, nom: "Recharge 20€", prixVente: 20 },
    { id: 4, nom: "Recharge 50€", prixVente: 50 }
  ];

  const handleOperatorSelect = (operator: any) => {
    setSelectedOperator(operator);
    setSelectedProduct(null);
    setAmount("");
    setIsOperatorMenuOpen(false);
  };

  const handleProductSelect = (product: any) => {
    setSelectedProduct(product);
    setAmount(product.prixVente.toString());
    setIsProductMenuOpen(false);
  };

  const validatePhoneNumber = () => {
    if (!phoneNumber) return false;
    // Basic validation, can be enhanced based on specific requirements
    return phoneNumber.length >= 10;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!selectedOperator) {
      toast.error("Veuillez sélectionner un opérateur");
      return;
    }

    if (!validatePhoneNumber()) {
      toast.error("Numéro de téléphone invalide");
      return;
    }

    if (!amount || parseFloat(amount) <= 0) {
      toast.error("Montant invalide");
      return;
    }

    setIsLoading(true);

    try {
      // In a real app, this would call the API
      // await api.post('/recharges', {
      //   operateurId: selectedOperator.id,
      //   numero: phoneNumber,
      //   montant: parseFloat(amount),
      //   produitId: selectedProduct?.id
      // });

      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));

      toast.success("Recharge effectuée avec succès");
      
      // Reset form
      setPhoneNumber("");
      setSelectedOperator(null);
      setSelectedProduct(null);
      setAmount("");
    } catch (error) {
      toast.error("Échec de la recharge. Veuillez réessayer.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Recharge Mobile</h1>
      </div>

      <div className="bg-white rounded-lg shadow p-6">
        <form onSubmit={handleSubmit} className="space-y-6">
          {/* Operator Selection */}
          <div className="space-y-2">
            <label className="block text-sm font-medium text-gray-700">
              Opérateur
            </label>
            <div className="relative">
              <button
                type="button"
                onClick={() => setIsOperatorMenuOpen(!isOperatorMenuOpen)}
                className="w-full flex items-center justify-between px-4 py-2 border rounded-md bg-white"
              >
                {selectedOperator ? (
                  <div className="flex items-center">
                    <img
                      src={`/uploads/images/logo/operateur/${selectedOperator.logo}`}
                      alt={selectedOperator.nom}
                      className="h-6 w-6 mr-2"
                    />
                    <span>{selectedOperator.nom}</span>
                  </div>
                ) : (
                  <span className="text-gray-400">Sélectionner un opérateur</span>
                )}
                <ChevronDown size={18} />
              </button>

              {isOperatorMenuOpen && (
                <div className="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border py-1">
                  {(operators || mockOperators).map((operator) => (
                    <button
                      key={operator.id}
                      type="button"
                      className="w-full flex items-center px-4 py-2 hover:bg-gray-100 text-left"
                      onClick={() => handleOperatorSelect(operator)}
                    >
                      <img
                        src={`/uploads/images/logo/operateur/${operator.logo}`}
                        alt={operator.nom}
                        className="h-6 w-6 mr-2"
                      />
                      <span>{operator.nom}</span>
                    </button>
                  ))}
                </div>
              )}
            </div>
          </div>

          {/* Phone Number Input */}
          <div className="space-y-2">
            <label className="block text-sm font-medium text-gray-700">
              Numéro de téléphone
            </label>
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Phone size={18} className="text-gray-400" />
              </div>
              <Input
                type="tel"
                value={phoneNumber}
                onChange={(e) => setPhoneNumber(e.target.value)}
                className="pl-10"
                placeholder="+33 X XX XX XX XX"
              />
            </div>
          </div>

          {/* Product Selection (if operator is selected) */}
          {selectedOperator && (
            <div className="space-y-2">
              <label className="block text-sm font-medium text-gray-700">
                Produit
              </label>
              <div className="relative">
                <button
                  type="button"
                  onClick={() => setIsProductMenuOpen(!isProductMenuOpen)}
                  className="w-full flex items-center justify-between px-4 py-2 border rounded-md bg-white"
                >
                  {selectedProduct ? (
                    <span>{selectedProduct.nom} - {formatCurrency(selectedProduct.prixVente)}</span>
                  ) : (
                    <span className="text-gray-400">Sélectionner un produit</span>
                  )}
                  <ChevronDown size={18} />
                </button>

                {isProductMenuOpen && (
                  <div className="absolute z-10 mt-1 w-full bg-white shadow-lg rounded-md border py-1">
                    {(products || mockProducts).map((product) => (
                      <button
                        key={product.id}
                        type="button"
                        className="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-100 text-left"
                        onClick={() => handleProductSelect(product)}
                      >
                        <span>{product.nom}</span>
                        <span className="font-medium">{formatCurrency(product.prixVente)}</span>
                      </button>
                    ))}
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Amount Input (if no product selected or for custom amount) */}
          {selectedOperator && !selectedProduct && (
            <div className="space-y-2">
              <label className="block text-sm font-medium text-gray-700">
                Montant
              </label>
              <div className="relative">
                <Input
                  type="number"
                  value={amount}
                  onChange={(e) => setAmount(e.target.value)}
                  min="0"
                  step="0.01"
                  placeholder="0.00"
                />
                <div className="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                  <span className="text-gray-500">€</span>
                </div>
              </div>
            </div>
          )}

          {/* Submit Button */}
          <div className="pt-4">
            <Button
              type="submit"
              disabled={isLoading || !selectedOperator || !validatePhoneNumber() || !amount}
              className="w-full"
            >
              {isLoading ? "Traitement en cours..." : "Effectuer la recharge"}
            </Button>
          </div>
        </form>
      </div>

      {/* Recent Recharges Section - Typically would show recent transactions */}
      <div className="bg-white rounded-lg shadow p-6">
        <h2 className="text-lg font-semibold mb-4">Informations</h2>
        <div className="space-y-4">
          <div className="flex items-start">
            <div className="flex-shrink-0">
              <Check size={18} className="text-green-500" />
            </div>
            <div className="ml-3">
              <p className="text-sm text-gray-700">
                Les recharges sont traitées instantanément et la valeur est créditée sur le compte mobile du client.
              </p>
            </div>
          </div>
          
          <div className="flex items-start">
            <div className="flex-shrink-0">
              <X size={18} className="text-red-500" />
            </div>
            <div className="ml-3">
              <p className="text-sm text-gray-700">
                Vérifiez bien le numéro du client avant validation. Les recharges ne peuvent pas être annulées une fois effectuées.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OfficeRecharge;
