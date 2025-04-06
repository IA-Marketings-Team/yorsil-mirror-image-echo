
import { Outlet } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";

const UserLayout = () => {
  const { authState, logout } = useAuth();
  const user = authState.user;

  return (
    <div className="min-h-screen bg-gray-50">
      <header className="bg-white shadow-sm">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
          <div className="flex items-center">
            <img 
              src="/lovable-uploads/ae8aec2f-ef82-4168-932c-49424a936919.png" 
              alt="Yorsil Logo" 
              className="h-8 mr-2" 
            />
            <h1 className="text-xl font-bold text-gray-800">Yorsil - Espace Utilisateur</h1>
          </div>
          <div className="flex items-center gap-4">
            {user && (
              <span className="text-sm font-medium text-gray-700">
                Bonjour, {user.prenom || user.nom}
              </span>
            )}
            <button 
              onClick={logout}
              className="text-sm font-medium text-gray-700 hover:text-gray-900"
            >
              Déconnexion
            </button>
          </div>
        </div>
      </header>
      <main className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <Outlet />
      </main>
    </div>
  );
};

export default UserLayout;
