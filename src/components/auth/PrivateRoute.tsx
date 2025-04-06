
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

  if (loading) {
    return <div className="flex items-center justify-center min-h-screen">Chargement...</div>;
  }

  if (!user) {
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  if (requiredRole && !user.roles.includes(requiredRole)) {
    // Redirect based on user role
    if (user.roles.includes("ROLE_ADMIN")) {
      return <Navigate to="/admin" replace />;
    }
    if (user.roles.includes("ROLE_BOUT")) {
      return <Navigate to="/office" replace />;
    }
    return <Navigate to="/login" replace />;
  }

  return <>{children}</>;
};

export default PrivateRoute;
