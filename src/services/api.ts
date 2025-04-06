
import axios from "axios";
import { supabase } from "@/integrations/supabase/client";

// Configuration de l'API
const API_CONFIG = {
  baseURL: import.meta.env.VITE_API_URL || "/api",
  headers: {
    "Content-Type": "application/json",
  },
  timeoutMs: 30000, // 30 secondes
  authTokenKey: "token",
  unauthorizedErrorCode: 401,
};

// Create an API instance with base configuration
export const api = axios.create({
  baseURL: API_CONFIG.baseURL,
  headers: API_CONFIG.headers,
  timeout: API_CONFIG.timeoutMs,
});

// Add request interceptor to inject token
api.interceptors.request.use(
  async (config) => {
    // Récupérer la session Supabase
    const { data } = await supabase.auth.getSession();
    const token = data.session?.access_token;
    
    if (token) {
      config.headers["Authorization"] = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Add response interceptor to handle errors
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response?.status === API_CONFIG.unauthorizedErrorCode) {
      // Unauthorized - sign out and redirect to login
      await supabase.auth.signOut();
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

// API utilities
export const apiUtils = {
  // Fonction pour formater les paramètres de requête
  formatQueryParams: (params: Record<string, any>): string => {
    const queryParams = new URLSearchParams();
    
    Object.entries(params).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        queryParams.append(key, String(value));
      }
    });
    
    const queryString = queryParams.toString();
    return queryString ? `?${queryString}` : '';
  },
  
  // Helper pour gérer les erreurs API de manière cohérente
  handleApiError: (error: any): string => {
    if (error.response?.data?.message) {
      return error.response.data.message;
    } else if (error.message) {
      return error.message;
    }
    return "Une erreur inconnue s'est produite";
  }
};
