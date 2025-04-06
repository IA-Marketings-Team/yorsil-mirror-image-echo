
import { create } from "zustand";
import { api } from "../services/api";
import { Operateur } from "../types";

interface OperateursState {
  operateurs: Operateur[];
  loading: boolean;
  error: string | null;
  fetchOperateurs: () => Promise<void>;
  addOperateur: (operateur: Partial<Operateur>) => Promise<void>;
  updateOperateur: (id: number, operateur: Partial<Operateur>) => Promise<void>;
}

export const useOperateursStore = create<OperateursState>((set) => ({
  operateurs: [],
  loading: false,
  error: null,
  
  fetchOperateurs: async () => {
    try {
      set({ loading: true });
      const response = await api.get("/operateurs");
      set({ operateurs: response.data, loading: false, error: null });
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec du chargement des opérateurs" 
      });
    }
  },
  
  addOperateur: async (operateur) => {
    try {
      set({ loading: true });
      const response = await api.post("/operateurs", operateur);
      
      // Update local state after successful API call
      set((state) => ({
        operateurs: [...state.operateurs, response.data],
        loading: false,
        error: null
      }));
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec de l'ajout de l'opérateur" 
      });
    }
  },
  
  updateOperateur: async (id, operateur) => {
    try {
      set({ loading: true });
      await api.put(`/operateurs/${id}`, operateur);
      
      // Update local state after successful API call
      set((state) => ({
        operateurs: state.operateurs.map((op) => 
          op.id === id ? { ...op, ...operateur } : op
        ),
        loading: false,
        error: null
      }));
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec de la mise à jour de l'opérateur" 
      });
    }
  }
}));
