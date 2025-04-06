
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { api } from "@/services/api";
import { Search, Calendar, MapPin, User, Ticket } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { formatCurrency, formatDate } from "@/lib/utils";
import { toast } from "react-toastify";

const OfficeBilleteries = () => {
  const [destination, setDestination] = useState("");
  const [dateDepart, setDateDepart] = useState("");
  const [isSearching, setIsSearching] = useState(false);
  const [searchResults, setSearchResults] = useState<any[]>([]);
  const [selectedTrip, setSelectedTrip] = useState<any>(null);
  const [passengerName, setPassengerName] = useState("");
  const [passengerPhone, setPassengerPhone] = useState("");
  const [isLoading, setIsLoading] = useState(false);

  // Mock search results for demonstration
  const mockSearchResults = [
    {
      id: 1,
      depart: "Paris",
      arrivee: "Lyon",
      date: "2024-04-15T10:00:00",
      prix: 45.50,
      places: 12,
      compagnie: "FastTravel",
    },
    {
      id: 2,
      depart: "Paris",
      arrivee: "Lyon",
      date: "2024-04-15T14:30:00",
      prix: 39.99,
      places: 5,
      compagnie: "EasyBus",
    },
    {
      id: 3,
      depart: "Paris",
      arrivee: "Lyon",
      date: "2024-04-15T18:00:00",
      prix: 52.00,
      places: 20,
      compagnie: "Premiere Classe",
    }
  ];

  const handleSearch = () => {
    if (!destination) {
      toast.error("Veuillez saisir une destination");
      return;
    }

    setIsSearching(true);

    // In a real app, this would call the API
    // api.get(`/billeteries/search?destination=${destination}&date=${dateDepart}`)
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
      setSearchResults(mockSearchResults);
      setIsSearching(false);
    }, 1000);
  };

  const handleSelectTrip = (trip: any) => {
    setSelectedTrip(trip);
  };

  const handleBookTicket = () => {
    if (!passengerName || !passengerPhone) {
      toast.error("Veuillez remplir tous les champs passager");
      return;
    }

    setIsLoading(true);

    // In a real app, this would call the API
    // api.post('/billeteries/book', {
    //   tripId: selectedTrip.id,
    //   passengerName,
    //   passengerPhone
    // })
    //   .then(response => {
    //     toast.success("Billet réservé avec succès");
    //     setSelectedTrip(null);
    //     setPassengerName("");
    //     setPassengerPhone("");
    //     setIsLoading(false);
    //   })
    //   .catch(error => {
    //     toast.error("Erreur lors de la réservation");
    //     setIsLoading(false);
    //   });

    // Simulate API call
    setTimeout(() => {
      toast.success("Billet réservé avec succès");
      setSelectedTrip(null);
      setPassengerName("");
      setPassengerPhone("");
      setIsLoading(false);
    }, 1500);
  };

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold">Billeteries</h1>
      </div>

      <div className="bg-white rounded-lg shadow p-6">
        <div className="space-y-4">
          <h2 className="text-lg font-semibold">Rechercher un trajet</h2>
          
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <MapPin size={18} className="text-gray-400" />
              </div>
              <Input
                type="text"
                value={destination}
                onChange={(e) => setDestination(e.target.value)}
                className="pl-10"
                placeholder="Destination (ex: Lyon)"
              />
            </div>
            
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Calendar size={18} className="text-gray-400" />
              </div>
              <Input
                type="date"
                value={dateDepart}
                onChange={(e) => setDateDepart(e.target.value)}
                className="pl-10"
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

      {searchResults.length > 0 && (
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="p-4 border-b">
            <h2 className="text-lg font-semibold">Résultats de la recherche</h2>
            <p className="text-sm text-gray-500">{searchResults.length} trajets trouvés</p>
          </div>
          
          <div className="divide-y">
            {searchResults.map((trip) => (
              <div
                key={trip.id}
                className={`p-4 hover:bg-gray-50 cursor-pointer ${
                  selectedTrip?.id === trip.id ? "bg-blue-50" : ""
                }`}
                onClick={() => handleSelectTrip(trip)}
              >
                <div className="flex flex-col md:flex-row md:items-center justify-between">
                  <div>
                    <h3 className="font-medium">{trip.depart} → {trip.arrivee}</h3>
                    <p className="text-sm text-gray-500">{formatDate(trip.date)}</p>
                    <p className="text-sm text-gray-500">{trip.compagnie}</p>
                  </div>
                  
                  <div className="mt-2 md:mt-0 md:text-right">
                    <p className="text-xl font-semibold">{formatCurrency(trip.prix)}</p>
                    <p className="text-sm text-gray-500">{trip.places} places disponibles</p>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}

      {selectedTrip && (
        <div className="bg-white rounded-lg shadow p-6">
          <h2 className="text-lg font-semibold mb-4">Réserver un billet</h2>
          
          <div className="mb-6 p-4 bg-blue-50 rounded-md border border-blue-200">
            <div className="flex justify-between">
              <div>
                <h3 className="font-medium">{selectedTrip.depart} → {selectedTrip.arrivee}</h3>
                <p className="text-sm text-gray-500">{formatDate(selectedTrip.date)}</p>
              </div>
              
              <div className="text-right">
                <p className="text-xl font-semibold">{formatCurrency(selectedTrip.prix)}</p>
              </div>
            </div>
          </div>
          
          <div className="space-y-4">
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <User size={18} className="text-gray-400" />
              </div>
              <Input
                type="text"
                value={passengerName}
                onChange={(e) => setPassengerName(e.target.value)}
                className="pl-10"
                placeholder="Nom du passager"
              />
            </div>
            
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <Phone size={18} className="text-gray-400" />
              </div>
              <Input
                type="tel"
                value={passengerPhone}
                onChange={(e) => setPassengerPhone(e.target.value)}
                className="pl-10"
                placeholder="Téléphone du passager"
              />
            </div>
            
            <div className="pt-4 flex justify-between">
              <Button
                variant="outline"
                onClick={() => setSelectedTrip(null)}
              >
                Annuler
              </Button>
              
              <Button
                onClick={handleBookTicket}
                disabled={isLoading}
                className="flex items-center gap-2"
              >
                <Ticket size={18} />
                {isLoading ? "Réservation en cours..." : "Réserver le billet"}
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default OfficeBilleteries;
