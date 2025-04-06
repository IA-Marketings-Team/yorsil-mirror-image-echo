
import { createContext, useContext, useState, ReactNode } from "react";
import { AuthContextType, AuthState, User } from "../types";
import { api } from "../services/api";
import { toast } from "react-toastify";

const initialState: AuthState = {
  user: null,
  token: localStorage.getItem("token"),
  loading: false,
  error: null,
};

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [authState, setAuthState] = useState<AuthState>(initialState);

  // Check if user is already logged in
  useState(() => {
    const checkAuth = async () => {
      if (authState.token) {
        try {
          setAuthState((prev) => ({ ...prev, loading: true }));
          const response = await api.get("/user/profile");
          setAuthState({
            user: response.data.user,
            token: authState.token,
            loading: false,
            error: null,
          });
        } catch (error) {
          localStorage.removeItem("token");
          setAuthState({
            user: null,
            token: null,
            loading: false,
            error: "Session expired",
          });
        }
      }
    };
    checkAuth();
  });

  const login = async (email: string, password: string) => {
    try {
      setAuthState((prev) => ({ ...prev, loading: true }));
      const response = await api.post("/login", { email, password });
      const { token, user } = response.data;
      
      localStorage.setItem("token", token);
      
      setAuthState({
        user,
        token,
        loading: false,
        error: null,
      });
    } catch (error: any) {
      setAuthState({
        user: null,
        token: null,
        loading: false,
        error: error.response?.data?.message || "Login failed",
      });
      toast.error(error.response?.data?.message || "Login failed");
    }
  };

  const logout = () => {
    localStorage.removeItem("token");
    setAuthState({
      user: null,
      token: null,
      loading: false,
      error: null,
    });
  };

  const register = async (userData: any) => {
    try {
      setAuthState((prev) => ({ ...prev, loading: true }));
      await api.post("/register", userData);
      setAuthState((prev) => ({ ...prev, loading: false }));
      toast.success("Registration successful! You can now login.");
    } catch (error: any) {
      setAuthState((prev) => ({
        ...prev,
        loading: false,
        error: error.response?.data?.message || "Registration failed",
      }));
      toast.error(error.response?.data?.message || "Registration failed");
    }
  };

  const forgotPassword = async (email: string) => {
    try {
      setAuthState((prev) => ({ ...prev, loading: true }));
      await api.post("/forgot-password", { email });
      setAuthState((prev) => ({ ...prev, loading: false }));
      toast.success("Password recovery email sent!");
    } catch (error: any) {
      setAuthState((prev) => ({
        ...prev,
        loading: false,
        error: error.response?.data?.message || "Failed to send recovery email",
      }));
      toast.error(error.response?.data?.message || "Failed to send recovery email");
    }
  };

  const resetPassword = async (token: string, password: string) => {
    try {
      setAuthState((prev) => ({ ...prev, loading: true }));
      await api.post(`/reset-password/${token}`, { password });
      setAuthState((prev) => ({ ...prev, loading: false }));
      toast.success("Password reset successful! You can now login.");
    } catch (error: any) {
      setAuthState((prev) => ({
        ...prev,
        loading: false,
        error: error.response?.data?.message || "Password reset failed",
      }));
      toast.error(error.response?.data?.message || "Password reset failed");
    }
  };

  return (
    <AuthContext.Provider
      value={{
        authState,
        login,
        logout,
        register,
        forgotPassword,
        resetPassword,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error("useAuth must be used within an AuthProvider");
  }
  return context;
}
