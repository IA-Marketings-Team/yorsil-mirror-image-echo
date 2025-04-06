
import React, { createContext, useState, useContext, ReactNode } from 'react';
import { supabase } from '@/integrations/supabase/client';
import { User, AuthState, LoginCredentials, RegisterData, AuthContextType } from '@/types/auth.types';
import { toast } from 'sonner';

const AuthContext = createContext<AuthContextType>({
  authState: {
    user: null,
    token: null,
    loading: false,
    error: null
  },
  login: async () => {},
  logout: () => {},
  register: async () => {},
  forgotPassword: async () => {},
  resetPassword: async () => {}
});

export const AuthProvider: React.FC<{ children: ReactNode }> = ({ children }) => {
  const [authState, setAuthState] = useState<AuthState>({
    user: null,
    token: null,
    loading: false,
    error: null
  });

  const login = async (email: string, password: string) => {
    setAuthState(prev => ({ ...prev, loading: true }));
    try {
      const { data, error } = await supabase.auth.signInWithPassword({ email, password });
      
      if (error) {
        setAuthState(prev => ({ ...prev, loading: false, error: error.message }));
        toast.error(error.message);
        return;
      }

      if (data.user) {
        const userProfile: User = {
          id: data.user.id,
          email: data.user.email || '',
          nom: data.user.user_metadata?.nom || '',
          prenom: data.user.user_metadata?.prenom || '',
          roles: data.user.user_metadata?.roles || ['ROLE_USER'],
          picture: data.user.user_metadata?.picture || '',
          sessionToken: data.session?.access_token || null
        };

        setAuthState({
          user: userProfile,
          token: data.session?.access_token || null,
          loading: false,
          error: null
        });

        toast.success('Connexion réussie');
      }
    } catch (error) {
      console.error('Login error:', error);
      setAuthState(prev => ({ 
        ...prev, 
        loading: false, 
        error: error instanceof Error ? error.message : 'Une erreur est survenue' 
      }));
      toast.error('Échec de la connexion');
    }
  };

  const logout = async () => {
    try {
      await supabase.auth.signOut();
      setAuthState({
        user: null,
        token: null,
        loading: false,
        error: null
      });
      toast.success('Déconnexion réussie');
    } catch (error) {
      console.error('Logout error:', error);
      toast.error('Échec de la déconnexion');
    }
  };

  const register = async (userData: RegisterData) => {
    setAuthState(prev => ({ ...prev, loading: true }));
    try {
      const { data, error } = await supabase.auth.signUp({
        email: userData.email,
        password: userData.password,
        options: {
          data: {
            nom: userData.nom,
            prenom: userData.prenom,
            telephone: userData.telephone
          }
        }
      });

      if (error) {
        setAuthState(prev => ({ ...prev, loading: false, error: error.message }));
        toast.error(error.message);
        return;
      }

      if (data.user) {
        toast.success('Inscription réussie');
      }
    } catch (error) {
      console.error('Register error:', error);
      setAuthState(prev => ({ 
        ...prev, 
        loading: false, 
        error: error instanceof Error ? error.message : 'Une erreur est survenue' 
      }));
      toast.error('Échec de l\'inscription');
    }
  };

  const forgotPassword = async (email: string) => {
    try {
      const { error } = await supabase.auth.resetPasswordForEmail(email);
      
      if (error) {
        toast.error(error.message);
        return;
      }

      toast.success('Un email de réinitialisation a été envoyé');
    } catch (error) {
      console.error('Forgot password error:', error);
      toast.error('Échec de la réinitialisation du mot de passe');
    }
  };

  const resetPassword = async (token: string, password: string) => {
    try {
      const { error } = await supabase.auth.updateUser({ 
        password 
      });

      if (error) {
        toast.error(error.message);
        return;
      }

      toast.success('Mot de passe réinitialisé avec succès');
    } catch (error) {
      console.error('Reset password error:', error);
      toast.error('Échec de la réinitialisation du mot de passe');
    }
  };

  return (
    <AuthContext.Provider value={{ 
      authState, 
      login, 
      logout, 
      register, 
      forgotPassword,
      resetPassword 
    }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
