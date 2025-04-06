
import { createContext, useState, ReactNode, useEffect } from "react";
import { AuthContextType, AuthState } from "../types/auth.types";
import { authService } from "../services/auth.service";
import { supabase } from "@/integrations/supabase/client";

const initialState: AuthState = {
  user: null,
  token: localStorage.getItem("token"),
  loading: false,
  error: null,
};

export const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [authState, setAuthState] = useState<AuthState>(initialState);

  // S'abonne aux changements d'état d'authentification Supabase
  useEffect(() => {
    // Définir l'écouteur d'état d'authentification en premier
    const { data: { subscription } } = supabase.auth.onAuthStateChange(
      async (event, session) => {
        if (session) {
          try {
            setAuthState((prev) => ({ ...prev, loading: true }));
            // Session existante, récupérer les données utilisateur
            const user = await authService.getProfile();
            setAuthState({
              user,
              token: session.access_token,
              loading: false,
              error: null,
            });
          } catch (error: any) {
            // Erreur lors de la récupération du profil utilisateur
            setAuthState({
              user: null,
              token: null,
              loading: false,
              error: error.message || "Erreur de récupération du profil",
            });
          }
        } else {
          // Pas de session, utilisateur déconnecté
          setAuthState({
            user: null,
            token: null,
            loading: false,
            error: null,
          });
        }
      }
    );

    // Vérifier s'il existe une session
    supabase.auth.getSession().then(({ data: { session } }) => {
      if (session) {
        authService.getProfile().then((user) => {
          setAuthState({
            user,
            token: session.access_token,
            loading: false,
            error: null,
          });
        }).catch((error) => {
          setAuthState({
            user: null,
            token: null,
            loading: false,
            error: error.message || "Erreur de récupération du profil",
          });
        });
      }
    });

    return () => {
      subscription.unsubscribe();
    };
  }, []);

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
        error: error.message || "Échec de connexion",
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
        error: error.message || "Échec d'inscription",
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
        error: error.message || "Échec d'envoi d'email de récupération",
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
        error: error.message || "Échec de réinitialisation du mot de passe",
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
