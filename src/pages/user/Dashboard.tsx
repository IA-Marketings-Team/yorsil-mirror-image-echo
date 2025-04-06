
import { useAuth } from "@/hooks/useAuth";

const UserDashboard = () => {
  const { authState } = useAuth();
  const { user } = authState;

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold">Tableau de bord utilisateur</h1>
      
      <div className="bg-white shadow rounded-lg p-6">
        <h2 className="text-lg font-semibold mb-4">Bienvenue dans votre espace utilisateur</h2>
        <p className="text-gray-600">
          Cette section est en cours de développement. Bientôt, vous pourrez accéder à tous les services disponibles pour votre compte.
        </p>
        
        {user && (
          <div className="mt-6 p-4 bg-gray-50 rounded-md">
            <h3 className="text-md font-medium">Informations sur votre compte</h3>
            <div className="mt-2 grid grid-cols-1 gap-2">
              <p><span className="font-medium">Nom:</span> {user.nom}</p>
              {user.prenom && <p><span className="font-medium">Prénom:</span> {user.prenom}</p>}
              <p><span className="font-medium">Email:</span> {user.email}</p>
              <p><span className="font-medium">Rôle:</span> Utilisateur standard</p>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default UserDashboard;
