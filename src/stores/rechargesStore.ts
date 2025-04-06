
import { create } from "zustand";
import { api } from "../services/api";
import { Recharge, RechargeFlexi } from "../types";

interface RechargesState {
  recharges: Recharge[];
  rechargesFlexi: RechargeFlexi[];
  loading: boolean;
  error: string | null;
  fetchRecharges: () => Promise<void>;
  fetchRechargesFlexi: () => Promise<void>;
  addRecharge: (recharge: Partial<Recharge>) => Promise<void>;
  addRechargeFlexi: (recharge: Partial<RechargeFlexi>) => Promise<void>;
}

export const useRechargesStore = create<RechargesState>((set) => ({
  recharges: [],
  rechargesFlexi: [],
  loading: false,
  error: null,
  
  fetchRecharges: async () => {
    try {
      set({ loading: true });
      const response = await api.get("/recharges");
      set({ recharges: response.data, loading: false, error: null });
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec du chargement des recharges" 
      });
    }
  },

  fetchRechargesFlexi: async () => {
    try {
      set({ loading: true });
      const response = await api.get("/recharges-flexi");
      set({ rechargesFlexi: response.data, loading: false, error: null });
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec du chargement des recharges flexi" 
      });
    }
  },
  
  addRecharge: async (recharge) => {
    try {
      set({ loading: true });
      const response = await api.post("/recharges", recharge);
      
      // Update local state after successful API call
      set((state) => ({
        recharges: [...state.recharges, response.data],
        loading: false,
        error: null
      }));
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec de l'ajout de la recharge" 
      });
    }
  },

  addRechargeFlexi: async (recharge) => {
    try {
      set({ loading: true });
      const response = await api.post("/recharges-flexi", recharge);
      
      // Update local state after successful API call
      set((state) => ({
        rechargesFlexi: [...state.rechargesFlexi, response.data],
        loading: false,
        error: null
      }));
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec de l'ajout de la recharge flexi" 
      });
    }
  }
}));
