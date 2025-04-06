
import { BrowserRouter as Router, Routes, Route, Navigate } from "react-router-dom";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "sonner";

// Routes constantes
import { ROUTES } from "./constants/routes";

// Auth
import Login from "./pages/auth/Login";
import Register from "./pages/auth/Register";
import ForgotPassword from "./pages/auth/ForgotPassword";
import ResetPassword from "./pages/auth/ResetPassword";

// Admin routes
import AdminDashboard from "./pages/admin/Dashboard";
import AdminUsers from "./pages/admin/Users";
import AdminBoutiques from "./pages/admin/Boutiques";
import AdminOperateurs from "./pages/admin/Operateurs";
import AdminPays from "./pages/admin/Pays";
import AdminProduits from "./pages/admin/Produits";
import AdminOffres from "./pages/admin/Offres";
import AdminJournal from "./pages/admin/Journal";

// Office (Boutique) routes
import OfficeDashboard from "./pages/office/Dashboard";
import OfficeRecharge from "./pages/office/Recharge";
import OfficeTransfertCredit from "./pages/office/TransfertCredit";
import OfficeBilleteries from "./pages/office/Billeteries";
import OfficeServices from "./pages/office/Services";
import OfficeHistory from "./pages/office/History";

// Components
import PrivateRoute from "./components/auth/PrivateRoute";
import AuthLayout from "./components/layout/AuthLayout";
import AdminLayout from "./components/layout/AdminLayout";
import OfficeLayout from "./components/layout/OfficeLayout";
import { useAuth } from "./hooks/useAuth";
import NotFound from "./pages/NotFound";

// Auth provider
import { AuthProvider } from "./contexts/AuthContext";

// Configuration QueryClient
const queryClientConfig = {
  defaultOptions: {
    queries: {
      refetchOnWindowFocus: false,
      retry: 1,
      staleTime: 5 * 60 * 1000, // 5 minutes
    },
  },
};

const queryClient = new QueryClient(queryClientConfig);

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <Router>
          <Routes>
            {/* Auth Routes */}
            <Route element={<AuthLayout />}>
              <Route path={ROUTES.AUTH.LOGIN} element={<Login />} />
              <Route path={ROUTES.AUTH.REGISTER} element={<Register />} />
              <Route path={ROUTES.AUTH.FORGOT_PASSWORD} element={<ForgotPassword />} />
              <Route path={ROUTES.AUTH.RESET_PASSWORD} element={<ResetPassword />} />
            </Route>
            
            {/* Admin Routes */}
            <Route path={ROUTES.ADMIN.ROOT} element={
              <PrivateRoute requiredRole="ROLE_ADMIN">
                <AdminLayout />
              </PrivateRoute>
            }>
              <Route index element={<AdminDashboard />} />
              <Route path="users" element={<AdminUsers />} />
              <Route path="boutiques" element={<AdminBoutiques />} />
              <Route path="operateurs" element={<AdminOperateurs />} />
              <Route path="pays" element={<AdminPays />} />
              <Route path="produits" element={<AdminProduits />} />
              <Route path="offres" element={<AdminOffres />} />
              <Route path="journal" element={<AdminJournal />} />
            </Route>
            
            {/* Office (Boutique) Routes */}
            <Route path={ROUTES.OFFICE.ROOT} element={
              <PrivateRoute requiredRole="ROLE_BOUT">
                <OfficeLayout />
              </PrivateRoute>
            }>
              <Route index element={<OfficeDashboard />} />
              <Route path="recharge" element={<OfficeRecharge />} />
              <Route path="transfert-credit" element={<OfficeTransfertCredit />} />
              <Route path="billeteries" element={<OfficeBilleteries />} />
              <Route path="services" element={<OfficeServices />} />
              <Route path="history" element={<OfficeHistory />} />
            </Route>
            
            {/* Default Route - Redirect to login or appropriate dashboard */}
            <Route path="/" element={<DefaultRedirect />} />
            
            {/* Catch-all route */}
            <Route path="*" element={<NotFound />} />
          </Routes>
        </Router>
        <Toaster position="top-right" richColors />
      </AuthProvider>
    </QueryClientProvider>
  );
}

// Fixed DefaultRedirect component to prevent infinite loops
const DefaultRedirect = () => {
  const { authState } = useAuth();
  const { user, loading } = authState;
  console.log("DefaultRedirect check:", { user, loading });
  
  // Show loading state while checking authentication
  if (loading) {
    return <div className="flex items-center justify-center min-h-screen">Chargement...</div>;
  }
  
  // Redirect to login if not authenticated
  if (!user) {
    return <Navigate to={ROUTES.AUTH.LOGIN} replace />;
  }
  
  // Role-based redirection
  if (user.roles.includes("ROLE_ADMIN")) {
    return <Navigate to={ROUTES.ADMIN.ROOT} replace />;
  }
  
  if (user.roles.includes("ROLE_BOUT")) {
    return <Navigate to={ROUTES.OFFICE.ROOT} replace />;
  }
  
  // For ROLE_USER, redirect to a dedicated page (or login until we have a user dashboard)
  if (user.roles.includes("ROLE_USER")) {
    // You can create a user dashboard later and redirect there
    // For now, just go to login to avoid infinite loops
    return <Navigate to={ROUTES.AUTH.LOGIN} replace />;
  }
  
  // Fallback - go to login page if no recognized roles
  return <Navigate to={ROUTES.AUTH.LOGIN} replace />;
};

export default App;
