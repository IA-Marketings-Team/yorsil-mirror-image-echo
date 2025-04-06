
import { Outlet } from "react-router-dom";
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "@/contexts/AuthContext";
import { 
  LayoutDashboard, 
  CreditCard, 
  ArrowLeftRight,
  Ticket,
  FileText,
  BookOpen,
  LogOut,
  Bell
} from "lucide-react";
import { cn } from "@/lib/utils";
import { formatCurrency } from "@/lib/utils";

const OfficeLayout = () => {
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const [showBalance, setShowBalance] = useState(false);
  const navigate = useNavigate();
  const { logout, authState } = useAuth();
  const { user } = authState;

  // Mock data - Would come from an API in a real app
  const boutiqueBalance = 1250.75;
  const notifications = 2;

  const menuItems = [
    { name: "Tableau de bord", icon: <LayoutDashboard size={20} />, path: "/office" },
    { name: "Recharge", icon: <CreditCard size={20} />, path: "/office/recharge" },
    { name: "Transfert Crédit", icon: <ArrowLeftRight size={20} />, path: "/office/transfert-credit" },
    { name: "Billeteries", icon: <Ticket size={20} />, path: "/office/billeteries" },
    { name: "Services", icon: <FileText size={20} />, path: "/office/services" },
    { name: "Journal", icon: <BookOpen size={20} />, path: "/office/history" },
  ];

  const toggleSidebar = () => setSidebarOpen(!sidebarOpen);
  const toggleBalanceVisibility = () => setShowBalance(!showBalance);
  
  const handleLogout = () => {
    logout();
    navigate("/login");
  };

  return (
    <div className="flex h-screen bg-gray-100">
      {/* Sidebar */}
      <aside 
        className={cn(
          "bg-white w-64 flex-shrink-0 border-r transition-all duration-300 ease-in-out", 
          sidebarOpen ? "block" : "hidden"
        )}
      >
        <div className="h-full flex flex-col">
          <div className="flex items-center justify-center h-16 border-b">
            <img 
              src="/lovable-uploads/ae8aec2f-ef82-4168-932c-49424a936919.png" 
              alt="Yorsil Logo" 
              className="h-8 w-auto"
            />
            <span className="ml-2 text-xl font-semibold text-gray-800">Boutique</span>
          </div>
          
          <nav className="flex-1 overflow-y-auto pt-5 px-2">
            <ul className="space-y-1">
              {menuItems.map((item) => (
                <li key={item.name}>
                  <button
                    className="w-full flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-md"
                    onClick={() => navigate(item.path)}
                  >
                    {item.icon}
                    <span className="ml-3">{item.name}</span>
                  </button>
                </li>
              ))}
            </ul>
          </nav>
          
          <div className="p-4 border-t">
            <div className="rounded-lg bg-gray-50 p-3">
              <div className="flex items-center justify-between">
                {user?.picture ? (
                  <img 
                    src={user.picture} 
                    alt={user.nom}
                    className="w-10 h-10 rounded-full"
                  />
                ) : (
                  <div className="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white">
                    {user?.nom?.charAt(0)}
                  </div>
                )}
                <div className="ml-3">
                  <p className="text-xs font-medium text-gray-500">Boutique</p>
                  <p className="text-sm font-semibold">{user?.nom}</p>
                </div>
              </div>
            </div>
            <button
              onClick={handleLogout}
              className="mt-4 w-full flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-md"
            >
              <LogOut size={20} />
              <span className="ml-3">Déconnexion</span>
            </button>
          </div>
        </div>
      </aside>

      {/* Main Content */}
      <div className="flex flex-col flex-1 overflow-hidden">
        {/* Header */}
        <header className="bg-white shadow-sm z-10">
          <div className="px-4 py-4 flex flex-col">
            <div className="flex items-center justify-between">
              <button 
                className="text-gray-500 focus:outline-none"
                onClick={toggleSidebar}
              >
                <svg className="h-6 w-6" viewBox="0 0 24 24" fill="none">
                  <path
                    d="M4 6H20M4 12H20M4 18H11"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  />
                </svg>
              </button>
              
              <div className="flex items-center">
                <div className="relative mr-4">
                  <button className="flex text-gray-500 focus:outline-none">
                    <Bell size={20} />
                  </button>
                  {notifications > 0 && (
                    <span className="absolute top-0 right-0 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-xs text-white">
                      {notifications}
                    </span>
                  )}
                </div>
                
                <div className="ml-4 flex items-center">
                  <span className="text-gray-800 mr-2">{user?.nom} {user?.prenom}</span>
                  <img 
                    className="h-8 w-8 rounded-full"
                    src={user?.picture || "https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y"}
                    alt="User profile"
                  />
                </div>
              </div>
            </div>
            
            <div className="flex items-center justify-between mt-4">
              <div className="flex items-center">
                <div className="text-sm">
                  <p className="text-gray-500">Nous contacter</p>
                  <a href="mailto:support@yorsil.com" className="text-blue-600 font-medium">support@yorsil.com</a>
                </div>
              </div>
              
              <div className="flex items-center">
                <div className="text-sm text-right">
                  <p className="text-gray-500">Solde</p>
                  <div className="flex items-center">
                    <p className="font-semibold">
                      {showBalance ? formatCurrency(boutiqueBalance) : "****** €"}
                    </p>
                    <button 
                      onClick={toggleBalanceVisibility}
                      className="ml-2 text-gray-500 focus:outline-none"
                    >
                      <svg 
                        className="h-5 w-5" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                      >
                        {showBalance ? (
                          <path 
                            strokeLinecap="round" 
                            strokeLinejoin="round" 
                            strokeWidth={2} 
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" 
                          />
                        ) : (
                          <path 
                            strokeLinecap="round" 
                            strokeLinejoin="round" 
                            strokeWidth={2} 
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" 
                          />
                        )}
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              
              <div className="flex items-center">
                <div className="text-sm">
                  <p className="text-gray-500">Téléphone</p>
                  <a href="tel:+33188611620" className="text-blue-600 font-medium">01 88 61 16 20</a>
                </div>
              </div>
            </div>
          </div>
        </header>

        {/* Main Content */}
        <main className="flex-1 overflow-auto p-6">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default OfficeLayout;
