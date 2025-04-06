
import { Navigate, useLocation } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";
import { Role } from "@/types/auth.types";
import { ROUTES } from "@/constants/routes";

interface PrivateRouteProps {
  children: React.ReactNode;
  requiredRole?: Role;
}

const PrivateRoute = ({ children, requiredRole }: PrivateRouteProps) => {
  const { authState } = useAuth();
  const { user, loading } = authState;
  const location = useLocation();

  console.log("PrivateRoute check:", { user, loading, requiredRole });

  // Show loading state while checking authentication
  if (loading) {
    return <div className="flex items-center justify-center min-h-screen">Chargement...</div>;
  }

  // Redirect to login if not authenticated
  if (!user) {
    return <Navigate to={ROUTES.AUTH.LOGIN} state={{ from: location }} replace />;
  }

  // Check for required role
  if (requiredRole && !user.roles.includes(requiredRole)) {
    // Redirect based on user's role
    if (user.roles.includes("ROLE_ADMIN")) {
      return <Navigate to={ROUTES.ADMIN.ROOT} replace />;
    }
    if (user.roles.includes("ROLE_BOUT")) {
      return <Navigate to={ROUTES.OFFICE.ROOT} replace />;
    }
    // For users with only ROLE_USER, redirect to login for now
    return <Navigate to={ROUTES.AUTH.LOGIN} replace />;
  }

  // Show protected content if everything is okay
  return <>{children}</>;
};

export default PrivateRoute;
