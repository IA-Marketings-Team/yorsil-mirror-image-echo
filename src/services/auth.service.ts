
import { api, apiUtils } from "./api";
import { toast } from "react-toastify";
import { User, LoginCredentials, RegisterData } from "@/types/auth.types";

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
      const response = await api.post(AUTH_CONFIG.endpoints.login, { email, password });
      const { token, user } = response.data;
      
      // Store token in localStorage
      localStorage.setItem(AUTH_CONFIG.storageTokenKey, token);
      toast.success(AUTH_MESSAGES.success.login);
      
      return { token, user };
    } catch (error: any) {
      const errorMessage = apiUtils.handleApiError(error) || AUTH_MESSAGES.error.login;
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },
  
  async register(userData: RegisterData): Promise<void> {
    try {
      await api.post(AUTH_CONFIG.endpoints.register, userData);
      toast.success(AUTH_MESSAGES.success.register);
    } catch (error: any) {
      const errorMessage = apiUtils.handleApiError(error) || AUTH_MESSAGES.error.register;
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },
  
  async getProfile(): Promise<User> {
    try {
      const response = await api.get(AUTH_CONFIG.endpoints.profile);
      return response.data.user;
    } catch (error: any) {
      const errorMessage = apiUtils.handleApiError(error) || AUTH_MESSAGES.error.getProfile;
      throw new Error(errorMessage);
    }
  },

  async forgotPassword(email: string): Promise<void> {
    try {
      await api.post(AUTH_CONFIG.endpoints.forgotPassword, { email });
      toast.success(AUTH_MESSAGES.success.forgotPassword);
    } catch (error: any) {
      const errorMessage = apiUtils.handleApiError(error) || AUTH_MESSAGES.error.forgotPassword;
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },

  async resetPassword(token: string, password: string): Promise<void> {
    try {
      await api.post(`${AUTH_CONFIG.endpoints.resetPassword}/${token}`, { password });
      toast.success(AUTH_MESSAGES.success.resetPassword);
    } catch (error: any) {
      const errorMessage = apiUtils.handleApiError(error) || AUTH_MESSAGES.error.resetPassword;
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },

  logout(): void {
    localStorage.removeItem(AUTH_CONFIG.storageTokenKey);
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
