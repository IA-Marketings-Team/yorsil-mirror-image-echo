
import { BrowserRouter as Router, Routes, Route, Navigate } from "react-router-dom";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";

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

// Auth provider
import { AuthProvider } from "./contexts/AuthContext";

const queryClient = new QueryClient();

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <AuthProvider>
        <Router>
          <Routes>
            {/* Auth Routes */}
            <Route element={<AuthLayout />}>
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Register />} />
              <Route path="/forgot-password" element={<ForgotPassword />} />
              <Route path="/reset-password/:token" element={<ResetPassword />} />
            </Route>
            
            {/* Admin Routes */}
            <Route path="/admin" element={
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
            <Route path="/office" element={
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
            
            {/* Default Route */}
            <Route path="/" element={<Navigate to="/login" replace />} />
          </Routes>
        </Router>
        <ToastContainer position="top-right" autoClose={5000} />
      </AuthProvider>
    </QueryClientProvider>
  );
}

export default App;
