
import { Navigate } from "react-router-dom";
import { useAuth } from "@/contexts/AuthContext";
import { Role } from "@/types";

interface PrivateRouteProps {
  children: React.ReactNode;
  requiredRole?: Role;
}

const PrivateRoute = ({ children, requiredRole }: PrivateRouteProps) => {
  const { authState } = useAuth();
  const { user, loading } = authState;

  if (loading) {
    return <div>Loading...</div>;
  }

  if (!user) {
    return <Navigate to="/login" replace />;
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
