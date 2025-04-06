
import { Navigate, useLocation } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";
import { Role } from "@/types/auth.types";

interface PrivateRouteProps {
  children: React.ReactNode;
  requiredRole?: Role;
}

const PrivateRoute = ({ children, requiredRole }: PrivateRouteProps) => {
  const { authState } = useAuth();
  const { user, loading } = authState;
  const location = useLocation();

  // Afficher l'état de chargement si les données utilisateur sont en cours de chargement
  if (loading) {
    return <div className="flex items-center justify-center min-h-screen">Chargement...</div>;
  }

  // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
  if (!user) {
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  // Vérifier le rôle requis
  if (requiredRole && !user.roles.includes(requiredRole)) {
    // Rediriger en fonction du rôle de l'utilisateur
    if (user.roles.includes("ROLE_ADMIN")) {
      return <Navigate to="/admin" replace />;
    }
    if (user.roles.includes("ROLE_BOUT")) {
      return <Navigate to="/office" replace />;
    }
    // Par défaut, retourner à la page de connexion
    return <Navigate to="/login" replace />;
  }

  // Afficher le contenu protégé si tout est en ordre
  return <>{children}</>;
};

export default PrivateRoute;
