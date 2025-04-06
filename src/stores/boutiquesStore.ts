
import { create } from "zustand";
import { api } from "../services/api";
import { Boutique } from "../types";

interface BoutiquesState {
  boutiques: Boutique[];
  loading: boolean;
  error: string | null;
  fetchBoutiques: () => Promise<void>;
  updateBoutiqueStatus: (id: number, isActive: boolean) => Promise<void>;
}

export const useBoutiquesStore = create<BoutiquesState>((set) => ({
  boutiques: [],
  loading: false,
  error: null,
  
  fetchBoutiques: async () => {
    try {
      set({ loading: true });
      const response = await api.get("/boutiques");
      set({ boutiques: response.data, loading: false, error: null });
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec du chargement des boutiques" 
      });
    }
  },
  
  updateBoutiqueStatus: async (id: number, isActive: boolean) => {
    try {
      set({ loading: true });
      await api.patch(`/boutiques/${id}/status`, { isActive });
      
      // Update local state after successful API call
      set((state) => ({
        boutiques: state.boutiques.map((boutique) => 
          boutique.id === id ? { ...boutique, isActive } : boutique
        ),
        loading: false,
        error: null
      }));
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec de la mise à jour de la boutique" 
      });
    }
  }
}));
