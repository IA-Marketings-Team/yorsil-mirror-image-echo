
import { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { Calendar, MapPin, Users, Phone as PhoneIcon, Clock, CreditCard } from "lucide-react";
import { api } from "@/services/api";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { formatCurrency } from "@/lib/utils";

const OfficeBilleteries = () => {
  const [searchParams, setSearchParams] = useState({
    departure: "",
    destination: "",
    date: "",
    passengers: 1
  });

  // Mock data for trips
  const journeys = [
    {
      id: 1,
      company: "FlixBus",
      departure: "Paris",
      destination: "Marseille",
      departureTime: "08:00",
      arrivalTime: "14:30",
      date: "2024-04-15",
      duration: "6h30",
      price: 35.99,
      seats: 12,
      logo: "/lovable-uploads/ae8aec2f-ef82-4168-932c-49424a936919.png"
    },
    {
      id: 2,
      company: "BlaBlaCar Bus",
      departure: "Paris",
      destination: "Marseille",
      departureTime: "09:15",
      arrivalTime: "15:00",
      date: "2024-04-15",
      duration: "5h45",
      price: 39.99,
      seats: 8,
      logo: "/lovable-uploads/ae8aec2f-ef82-4168-932c-49424a936919.png"
    },
    {
      id: 3,
      company: "FlixBus",
      departure: "Paris",
      destination: "Marseille",
      departureTime: "12:30",
      arrivalTime: "18:45",
      date: "2024-04-15",
      duration: "6h15",
      price: 32.50,
      seats: 20,
      logo: "/lovable-uploads/ae8aec2f-ef82-4168-932c-49424a936919.png"
    },
    {
      id: 4,
      company: "BlaBlaCar Bus",
      departure: "Paris",
      destination: "Marseille",
      departureTime: "16:00",
      arrivalTime: "22:15",
      date: "2024-04-15",
      duration: "6h15",
      price: 29.99,
      seats: 15,
      logo: "/lovable-uploads/ae8aec2f-ef82-4168-932c-49424a936919.png"
    }
  ];

  // Common French cities
  const popularCities = [
    "Paris", "Marseille", "Lyon", "Toulouse", "Nice", 
    "Nantes", "Strasbourg", "Montpellier", "Bordeaux", "Lille"
  ];

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    console.log("Searching for journeys with params:", searchParams);
    // Would trigger API call in a real app
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setSearchParams(prev => ({ ...prev, [name]: value }));
  };

  const handleBooking = (journey: any) => {
    console.log("Booking journey:", journey);
    // Would navigate to booking page in a real app
  };

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Billeteries</h1>
        <p className="text-gray-500 mt-1">Recherchez et réservez des billets de bus pour vos clients</p>
      </div>

      <div className="bg-white rounded-lg shadow-sm p-6">
        <form onSubmit={handleSearch} className="space-y-4">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
              <label htmlFor="departure" className="block text-sm font-medium text-gray-700 mb-1">
                Départ
              </label>
              <Input
                id="departure"
                name="departure"
                value={searchParams.departure}
                onChange={handleInputChange}
                placeholder="Ville de départ"
                list="departureCities"
                required
              />
              <datalist id="departureCities">
                {popularCities.map(city => (
                  <option key={`departure-${city}`} value={city} />
                ))}
              </datalist>
            </div>
            
            <div>
              <label htmlFor="destination" className="block text-sm font-medium text-gray-700 mb-1">
                Destination
              </label>
              <Input
                id="destination"
                name="destination"
                value={searchParams.destination}
                onChange={handleInputChange}
                placeholder="Ville d'arrivée"
                list="destinationCities"
                required
              />
              <datalist id="destinationCities">
                {popularCities.map(city => (
                  <option key={`destination-${city}`} value={city} />
                ))}
              </datalist>
            </div>
            
            <div>
              <label htmlFor="date" className="block text-sm font-medium text-gray-700 mb-1">
                Date
              </label>
              <Input
                id="date"
                name="date"
                type="date"
                value={searchParams.date}
                onChange={handleInputChange}
                min={new Date().toISOString().split('T')[0]}
                required
              />
            </div>
            
            <div>
              <label htmlFor="passengers" className="block text-sm font-medium text-gray-700 mb-1">
                Passagers
              </label>
              <select
                id="passengers"
                name="passengers"
                value={searchParams.passengers}
                onChange={handleInputChange}
                className="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
              >
                {[1, 2, 3, 4, 5, 6, 7, 8].map(num => (
                  <option key={num} value={num}>{num} {num === 1 ? 'passager' : 'passagers'}</option>
                ))}
              </select>
            </div>
          </div>
          
          <div className="flex justify-end">
            <Button type="submit" className="px-6">
              Rechercher
            </Button>
          </div>
        </form>
      </div>

      {/* For demo purposes, we'll show results regardless of search */}
      <div className="space-y-4">
        <h2 className="text-lg font-semibold">Résultats de recherche</h2>
        
        {journeys.length > 0 ? (
          <div className="space-y-4">
            {journeys.map(journey => (
              <div key={journey.id} className="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  <div className="flex items-center space-x-4">
                    <div className="flex-shrink-0">
                      <img src={journey.logo} alt={journey.company} className="h-12 w-12 object-contain" />
                    </div>
                    <div>
                      <h3 className="font-medium">{journey.company}</h3>
                      <div className="text-sm text-gray-500">
                        <Clock className="inline-block h-4 w-4 mr-1" />
                        {journey.duration}
                      </div>
                    </div>
                  </div>
                  
                  <div className="flex flex-col">
                    <div className="flex items-center mb-2">
                      <MapPin className="h-4 w-4 text-gray-500 mr-1" />
                      <span className="text-sm">{journey.departure}</span>
                      <span className="mx-2 text-gray-500">•</span>
                      <span className="font-medium">{journey.departureTime}</span>
                    </div>
                    <div className="flex items-center">
                      <MapPin className="h-4 w-4 text-gray-500 mr-1" />
                      <span className="text-sm">{journey.destination}</span>
                      <span className="mx-2 text-gray-500">•</span>
                      <span className="font-medium">{journey.arrivalTime}</span>
                    </div>
                  </div>
                  
                  <div className="flex items-center justify-between md:justify-end">
                    <div className="flex flex-col mr-4">
                      <span className="text-lg font-bold">{formatCurrency(journey.price)}</span>
                      <span className="text-xs text-gray-500">
                        <Users className="inline-block h-3 w-3 mr-1" />
                        {journey.seats} places disponibles
                      </span>
                    </div>
                    <Button onClick={() => handleBooking(journey)}>
                      Réserver
                    </Button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="text-center py-8 bg-white rounded-lg shadow-sm">
            <p>Aucun trajet trouvé. Veuillez modifier vos critères de recherche.</p>
          </div>
        )}
      </div>
    </div>
  );
};

export default OfficeBilleteries;
