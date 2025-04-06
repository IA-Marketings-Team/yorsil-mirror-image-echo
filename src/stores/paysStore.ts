
import { create } from "zustand";
import { api } from "../services/api";
import { Pays } from "../types";

interface PaysState {
  pays: Pays[];
  loading: boolean;
  error: string | null;
  fetchPays: () => Promise<void>;
  updatePaysApi: (id: number, isApi: boolean, typeApi?: string) => Promise<void>;
}

export const usePaysStore = create<PaysState>((set) => ({
  pays: [],
  loading: false,
  error: null,
  
  fetchPays: async () => {
    try {
      set({ loading: true });
      const response = await api.get("/pays");
      set({ pays: response.data, loading: false, error: null });
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec du chargement des pays" 
      });
    }
  },
  
  updatePaysApi: async (id, isApi, typeApi) => {
    try {
      set({ loading: true });
      await api.patch(`/pays/${id}/api`, { isApi, typeApi });
      
      // Update local state after successful API call
      set((state) => ({
        pays: state.pays.map((p) => 
          p.id === id ? { ...p, isApi, typeApi } : p
        ),
        loading: false,
        error: null
      }));
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec de la mise à jour du pays" 
      });
    }
  }
}));
