
import { Outlet } from "react-router-dom";
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";
import { 
  LayoutDashboard, 
  Users, 
  Store, 
  FileText, 
  Globe, 
  Package, 
  Gift, 
  BookOpen, 
  LogOut 
} from "lucide-react";
import { cn } from "@/lib/utils";

const AdminLayout = () => {
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const navigate = useNavigate();
  const { logout, authState } = useAuth();
  const { user } = authState;

  const menuItems = [
    { name: "Tableau de bord", icon: <LayoutDashboard size={20} />, path: "/admin" },
    { name: "Utilisateurs", icon: <Users size={20} />, path: "/admin/users" },
    { name: "Boutiques", icon: <Store size={20} />, path: "/admin/boutiques" },
    { name: "Opérateurs", icon: <FileText size={20} />, path: "/admin/operateurs" },
    { name: "Pays", icon: <Globe size={20} />, path: "/admin/pays" },
    { name: "Produits", icon: <Package size={20} />, path: "/admin/produits" },
    { name: "Offres", icon: <Gift size={20} />, path: "/admin/offres" },
    { name: "Journal", icon: <BookOpen size={20} />, path: "/admin/journal" },
  ];

  const toggleSidebar = () => setSidebarOpen(!sidebarOpen);
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
            <span className="ml-2 text-xl font-semibold text-gray-800">Admin</span>
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
          
          <div className="border-t p-4">
            <button
              onClick={handleLogout}
              className="w-full flex items-center px-4 py-2 text-red-600 hover:bg-red-50 rounded-md"
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
          <div className="px-4 py-4 flex items-center justify-between">
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
              <div className="relative">
                <button className="flex text-gray-500 focus:outline-none">
                  <svg className="h-6 w-6" viewBox="0 0 24 24" fill="none">
                    <path
                      d="M15 17H9V18C9 19.6569 10.3431 21 12 21C13.6569 21 15 19.6569 15 18V17Z"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    />
                    <path
                      d="M12 3C10.3431 3 9 4.34315 9 6V12H15V6C15 4.34315 13.6569 3 12 3Z"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    />
                    <path
                      d="M19 12C19 13.8565 18.4888 15.637 17.5355 17.1056C16.5822 18.5741 15.2412 19.6528 13.6569 20.1872"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    />
                    <path
                      d="M5 12C5 13.8565 5.51117 15.637 6.46447 17.1056C7.41778 18.5741 8.75877 19.6528 10.3431 20.1872"
                      stroke="currentColor"
                      strokeWidth="2"
                      strokeLinecap="round"
                      strokeLinejoin="round"
                    />
                  </svg>
                </button>
                <span className="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
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
        </header>

        {/* Main Content */}
        <main className="flex-1 overflow-auto p-6">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default AdminLayout;
