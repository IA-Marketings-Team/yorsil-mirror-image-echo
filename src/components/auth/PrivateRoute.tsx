
import { Navigate } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";
import { Role } from "@/types";
import { useEffect } from "react";

interface PrivateRouteProps {
  children: React.ReactNode;
  requiredRole?: Role;
}

const PrivateRoute = ({ children, requiredRole }: PrivateRouteProps) => {
  const { authState } = useAuth();
  const { user, loading } = authState;

  useEffect(() => {
    if (user) {
      console.info("User authenticated, redirecting based on role:", user.roles);
    }
  }, [user]);

  if (loading) {
    return <div>Loading...</div>;
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  // Si aucun rôle spécifique n'est requis, autoriser l'accès
  if (!requiredRole) {
    return <>{children}</>;
  }

  // Si l'utilisateur a le rôle requis, autoriser l'accès
  if (user.roles.includes(requiredRole)) {
    return <>{children}</>;
  }

  // Redirection basée sur le rôle de l'utilisateur
  if (user.roles.includes("ROLE_ADMIN")) {
    return <Navigate to="/admin" replace />;
  }
  if (user.roles.includes("ROLE_BOUT")) {
    return <Navigate to="/office" replace />;
  }
  if (user.roles.includes("ROLE_USER")) {
    return <Navigate to="/user" replace />;
  }

  // Par défaut, rediriger vers la page de connexion
  return <Navigate to="/login" replace />;
};

export default PrivateRoute;
