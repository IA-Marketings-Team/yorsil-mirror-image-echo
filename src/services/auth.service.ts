
import { supabase } from "@/integrations/supabase/client";
import { toast } from "react-toastify";
import { User, LoginCredentials, RegisterData, Role } from "@/types/auth.types";

export interface AuthResponse {
  token: string;
  user: User;
}

// Messages d'erreur et de succès
const AUTH_MESSAGES = {
  success: {
    login: "Connexion réussie!",
    register: "Inscription réussie! Vous pouvez maintenant vous connecter.",
    forgotPassword: "Email de récupération envoyé!",
    resetPassword: "Mot de passe réinitialisé avec succès! Vous pouvez maintenant vous connecter."
  },
  error: {
    login: "Échec de la connexion",
    register: "Échec de l'inscription",
    forgotPassword: "Échec de l'envoi de l'email de récupération",
    resetPassword: "Échec de la réinitialisation du mot de passe",
    getProfile: "Échec de la récupération du profil utilisateur"
  }
};

// Configuration
const AUTH_CONFIG = {
  storageTokenKey: "token",
  endpoints: {
    login: "/login",
    register: "/register",
    profile: "/user/profile",
    forgotPassword: "/forgot-password",
    resetPassword: "/reset-password"
  }
};

export const authService = {
  async login({ email, password }: LoginCredentials): Promise<AuthResponse> {
    try {
      const { data, error } = await supabase.auth.signInWithPassword({
        email,
        password
      });
      
      if (error) throw new Error(error.message);
      if (!data.user) throw new Error("Aucun utilisateur trouvé");
      
      // Récupérer les données utilisateur complètes de la table users
      const { data: userData, error: userError } = await supabase
        .from('users')
        .select('id, email, nom, prenom, roles, picture, session_token')
        .eq('id', data.user.id)
        .single();
      
      if (userError) throw new Error(userError.message);
      if (!userData) throw new Error('Utilisateur non trouvé');
      
      const token = data.session?.access_token || '';
      // Store token in localStorage
      localStorage.setItem(AUTH_CONFIG.storageTokenKey, token);
      toast.success(AUTH_MESSAGES.success.login);
      
      // Using type assertion to handle type mismatch
      return { 
        token, 
        user: {
          id: userData.id,
          email: userData.email,
          nom: userData.nom,
          prenom: userData.prenom,
          roles: (userData.roles || ["ROLE_USER"]) as Role[],
          picture: userData.picture,
          sessionToken: userData.session_token
        } 
      };
    } catch (error: any) {
      const errorMessage = error.message || AUTH_MESSAGES.error.login;
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },
  
  async register(userData: RegisterData): Promise<void> {
    try {
      // Créer l'utilisateur dans supabase auth
      const { data, error } = await supabase.auth.signUp({
        email: userData.email,
        password: userData.password
      });
      
      if (error) throw new Error(error.message);
      if (!data.user) throw new Error('Erreur lors de la création de l\'utilisateur');
      
      // Créer l'entrée dans la table users avec les informations supplémentaires
      const { error: userError } = await supabase
        .from('users')
        .insert([{
          id: data.user.id,
          email: userData.email,
          nom: userData.nom,
          prenom: userData.prenom,
          roles: ['ROLE_USER'] // Rôle par défaut
        }] as any);
      
      if (userError) throw new Error(userError.message);
      
      toast.success(AUTH_MESSAGES.success.register);
    } catch (error: any) {
      const errorMessage = error.message || AUTH_MESSAGES.error.register;
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },
  
  async getProfile(): Promise<User> {
    try {
      // Récupérer l'utilisateur actuel
      const { data: authData, error: authError } = await supabase.auth.getUser();
      
      if (authError) throw new Error(authError.message);
      if (!authData.user) throw new Error('Utilisateur non connecté');
      
      // Récupérer les données complètes de l'utilisateur depuis la table users
      const { data, error } = await supabase
        .from('users')
        .select('id, email, nom, prenom, roles, picture, session_token')
        .eq('id', authData.user.id)
        .single();
      
      if (error) throw new Error(error.message);
      if (!data) throw new Error('Utilisateur introuvable');
      
      // Using type assertion to handle type mismatch
      return {
        id: data.id,
        email: data.email,
        nom: data.nom,
        prenom: data.prenom,
        roles: (data.roles || ["ROLE_USER"]) as Role[],
        picture: data.picture,
        sessionToken: data.session_token
      };
    } catch (error: any) {
      const errorMessage = error.message || AUTH_MESSAGES.error.getProfile;
      throw new Error(errorMessage);
    }
  },

  async forgotPassword(email: string): Promise<void> {
    try {
      const { error } = await supabase.auth.resetPasswordForEmail(email, {
        redirectTo: window.location.origin + '/reset-password',
      });
      
      if (error) throw new Error(error.message);
      toast.success(AUTH_MESSAGES.success.forgotPassword);
    } catch (error: any) {
      const errorMessage = error.message || AUTH_MESSAGES.error.forgotPassword;
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },

  async resetPassword(token: string, password: string): Promise<void> {
    try {
      const { error } = await supabase.auth.updateUser({
        password: password
      });
      
      if (error) throw new Error(error.message);
      toast.success(AUTH_MESSAGES.success.resetPassword);
    } catch (error: any) {
      const errorMessage = error.message || AUTH_MESSAGES.error.resetPassword;
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },

  logout(): void {
    supabase.auth.signOut().then(() => {
      localStorage.removeItem(AUTH_CONFIG.storageTokenKey);
    });
  },
  
  // Vérifier si l'utilisateur est connecté
  isAuthenticated(): boolean {
    return !!localStorage.getItem(AUTH_CONFIG.storageTokenKey);
  },
  
  // Récupérer le token actuel
  getToken(): string | null {
    return localStorage.getItem(AUTH_CONFIG.storageTokenKey);
  }
};
