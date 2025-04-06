
import { createContext, useState, ReactNode, useEffect } from "react";
import { AuthContextType, AuthState } from "../types/auth.types";
import { authService } from "../services/auth.service";

const initialState: AuthState = {
  user: null,
  token: localStorage.getItem("token"),
  loading: false,
  error: null,
};

export const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [authState, setAuthState] = useState<AuthState>(initialState);

  // Check if user is already logged in
  useEffect(() => {
    const checkAuth = async () => {
      if (authState.token) {
        try {
          setAuthState((prev) => ({ ...prev, loading: true }));
          const user = await authService.getProfile();
          setAuthState({
            user,
            token: authState.token,
            loading: false,
            error: null,
          });
        } catch (error) {
          authService.logout();
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
  }, [authState.token]);

  const login = async (email: string, password: string) => {
    try {
      setAuthState((prev) => ({ ...prev, loading: true }));
      const { token, user } = await authService.login({ email, password });
      
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
        error: error.message || "Login failed",
      });
    }
  };

  const logout = () => {
    authService.logout();
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
      await authService.register(userData);
      setAuthState((prev) => ({ ...prev, loading: false }));
    } catch (error: any) {
      setAuthState((prev) => ({
        ...prev,
        loading: false,
        error: error.message || "Registration failed",
      }));
    }
  };

  const forgotPassword = async (email: string) => {
    try {
      setAuthState((prev) => ({ ...prev, loading: true }));
      await authService.forgotPassword(email);
      setAuthState((prev) => ({ ...prev, loading: false }));
    } catch (error: any) {
      setAuthState((prev) => ({
        ...prev,
        loading: false,
        error: error.message || "Failed to send recovery email",
      }));
    }
  };

  const resetPassword = async (token: string, password: string) => {
    try {
      setAuthState((prev) => ({ ...prev, loading: true }));
      await authService.resetPassword(token, password);
      setAuthState((prev) => ({ ...prev, loading: false }));
    } catch (error: any) {
      setAuthState((prev) => ({
        ...prev,
        loading: false,
        error: error.message || "Password reset failed",
      }));
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
