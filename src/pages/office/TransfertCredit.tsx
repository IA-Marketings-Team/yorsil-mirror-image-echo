
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { api } from "@/services/api";
import { ChevronDown, Phone, DollarSign, ArrowLeftRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { formatCurrency } from "@/lib/utils";
import { toast } from "react-toastify";

const OfficeTransfertCredit = () => {
  const [selectedOperator, setSelectedOperator] = useState<any>(null);
  const [phoneNumber, setPhoneNumber] = useState("");
  const [amount, setAmount] = useState("");
  const [isOperatorMenuOpen, setIsOperatorMenuOpen] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  // Fetch operators list
  const { data: operators } = useQuery({
    queryKey: ['transfert-operators'],
    queryFn: async () => {
      const response = await api.get('/operators');
      return response.data;
    }
  });

  // Mock data for when API is not available
  const mockOperators = [
    { id: 1, nom: "Orange", logo: "orange_logo.png", type: "Mobile" },
    { id: 2, nom: "SFR", logo: "sfr_logo.png", type: "Mobile" },
    { id: 3, nom: "Bouygues", logo: "bouygues_logo.png", type: "Mobile" },
    { id: 4, nom: "Free", logo: "free_logo.png", type: "Mobile" }
  ];

  const handleOperatorSelect = (operator: any) => {
    setSelectedOperator(operator);
    setIsOperatorMenuOpen(false);
  };

  const validatePhoneNumber = () => {
    if (!phoneNumber) return false;
    // Basic validation, can be enhanced based on specific requirements
    return phoneNumber.length >= 10;
  };

  const validateAmount = () => {
    if (!amount) return false;
    const numAmount = parseFloat(amount);
    return !isNaN(numAmount) && numAmount > 0;
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

    if (!validateAmount()) {
      toast.error("Montant invalide");
      return;
    }

    setIsLoading(true);

    try {
      // In a real app, this would call the API
      // await api.post('/transfert-credit', {
      //   operateurId: selectedOperator.id,
      //   numero: phoneNumber,
      //   montant: parseFloat(amount)
      // });

      // Simulate API call
      await new Promise(resolve => setTimeout(resolve, 1000));

      toast.success("Transfert de crédit effectué avec succès");
      
      // Reset form
      setPhoneNumber("");
      setSelectedOperator(null);
      setAmount("");
    } catch (error) {
      toast.error("Échec du transfert. Veuillez réessayer.");
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Transfert de Crédit</h1>
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

          {/* Amount Input */}
          <div className="space-y-2">
            <label className="block text-sm font-medium text-gray-700">
              Montant à transférer
            </label>
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <DollarSign size={18} className="text-gray-400" />
              </div>
              <Input
                type="number"
                value={amount}
                onChange={(e) => setAmount(e.target.value)}
                className="pl-10"
                min="0"
                step="0.01"
                placeholder="0.00"
              />
              <div className="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span className="text-gray-500">€</span>
              </div>
            </div>
          </div>

          {/* Submit Button */}
          <div className="pt-4">
            <Button
              type="submit"
              disabled={isLoading || !selectedOperator || !validatePhoneNumber() || !validateAmount()}
              className="w-full"
            >
              {isLoading ? "Traitement en cours..." : "Effectuer le transfert"}
            </Button>
          </div>
        </form>
      </div>

      {/* Information Section */}
      <div className="bg-white rounded-lg shadow p-6">
        <h2 className="text-lg font-semibold mb-4">Comment ça marche ?</h2>
        
        <div className="space-y-4">
          <div className="flex items-start">
            <div className="flex-shrink-0 bg-blue-100 rounded-full p-2">
              <ArrowLeftRight size={20} className="text-blue-600" />
            </div>
            <div className="ml-3">
              <p className="text-sm font-medium text-gray-900">Transfert instantané</p>
              <p className="mt-1 text-sm text-gray-500">
                Le crédit est transféré instantanément sur le compte mobile du client.
              </p>
            </div>
          </div>
          
          <div className="flex items-start">
            <div className="flex-shrink-0 bg-blue-100 rounded-full p-2">
              <Phone size={20} className="text-blue-600" />
            </div>
            <div className="ml-3">
              <p className="text-sm font-medium text-gray-900">Vérification du numéro</p>
              <p className="mt-1 text-sm text-gray-500">
                Vérifiez bien le numéro du client avant validation. Les transferts ne peuvent pas être annulés.
              </p>
            </div>
          </div>
          
          <div className="flex items-start">
            <div className="flex-shrink-0 bg-blue-100 rounded-full p-2">
              <DollarSign size={20} className="text-blue-600" />
            </div>
            <div className="ml-3">
              <p className="text-sm font-medium text-gray-900">Frais de service</p>
              <p className="mt-1 text-sm text-gray-500">
                Des frais de service peuvent s'appliquer selon l'opérateur et le montant du transfert.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OfficeTransfertCredit;
