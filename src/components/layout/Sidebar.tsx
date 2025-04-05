
import { useState } from "react";
import { Link } from "react-router-dom";
import { 
  LayoutDashboard, 
  Users, 
  Store, 
  FileText, 
  Globe, 
  PackageOpen, 
  Euro, 
  Presentation, 
  ArrowLeftRight, 
  List, 
  Phone, 
  Gift, 
  BookOpen, 
  Menu
} from "lucide-react";

interface NavItemProps {
  icon: React.ReactNode;
  label: string;
  active?: boolean;
  hasChildren?: boolean;
}

const NavItem = ({ icon, label, active, hasChildren = false }: NavItemProps) => {
  return (
    <Link 
      to="#" 
      className={`flex items-center p-3 ${active ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'} rounded-md`}
    >
      <div className="mr-3">{icon}</div>
      <span>{label}</span>
      {hasChildren && <span className="ml-auto">&gt;</span>}
    </Link>
  );
};

const Sidebar = () => {
  const [collapsed, setCollapsed] = useState(false);

  return (
    <div className={`h-screen bg-white border-r transition-all duration-300 ${collapsed ? 'w-16' : 'w-64'}`}>
      <div className="flex items-center p-4 border-b">
        <Link to="/" className="flex items-center">
          <img 
            src="/lovable-uploads/ae8aec2f-ef82-4168-932c-49424a936919.png" 
            alt="Yorsil Logo" 
            className="h-8"
          />
          {!collapsed && <span className="ml-2 text-xl font-bold">Yorsil.</span>}
        </Link>
        <button 
          onClick={() => setCollapsed(!collapsed)} 
          className="ml-auto p-1 rounded-md hover:bg-gray-100"
        >
          <Menu size={20} />
        </button>
      </div>
      
      <div className="p-2 space-y-1">
        <NavItem 
          icon={<LayoutDashboard size={20} />} 
          label="Tableau de bord" 
          active={true}
        />
        <NavItem 
          icon={<Users size={20} />} 
          label="Administrateurs"
        />
        <NavItem 
          icon={<Store size={20} />} 
          label="Boutiques"
        />
        <NavItem 
          icon={<FileText size={20} />} 
          label="Services"
        />
        <NavItem 
          icon={<Globe size={20} />} 
          label="Pays"
        />
        <NavItem 
          icon={<PackageOpen size={20} />} 
          label="Gestion de produits" 
          hasChildren={true}
        />
        <NavItem 
          icon={<Euro size={20} />} 
          label="Percepteurs"
        />
        <NavItem 
          icon={<Presentation size={20} />} 
          label="Slides"
        />
        <NavItem 
          icon={<ArrowLeftRight size={20} />} 
          label="Taux de Change"
        />
        <NavItem 
          icon={<List size={20} />} 
          label="Grilles tarifaires" 
          hasChildren={true}
        />
        <NavItem 
          icon={<Phone size={20} />} 
          label="Gestion opérateurs" 
          hasChildren={true}
        />
        <NavItem 
          icon={<Gift size={20} />} 
          label="Offres" 
          hasChildren={true}
        />
        <NavItem 
          icon={<BookOpen size={20} />} 
          label="Journal" 
          hasChildren={true}
        />
      </div>
    </div>
  );
};

export default Sidebar;
