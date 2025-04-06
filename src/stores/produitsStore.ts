
import { create } from "zustand";
import { api } from "../services/api";
import { Produit } from "../types";

interface ProduitsState {
  produits: Produit[];
  loading: boolean;
  error: string | null;
  fetchProduits: () => Promise<void>;
  addProduit: (produit: Partial<Produit>) => Promise<void>;
  updateProduit: (id: number, produit: Partial<Produit>) => Promise<void>;
}

export const useProduitsStore = create<ProduitsState>((set) => ({
  produits: [],
  loading: false,
  error: null,
  
  fetchProduits: async () => {
    try {
      set({ loading: true });
      const response = await api.get("/produits");
      set({ produits: response.data, loading: false, error: null });
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec du chargement des produits" 
      });
    }
  },
  
  addProduit: async (produit) => {
    try {
      set({ loading: true });
      const response = await api.post("/produits", produit);
      
      // Update local state after successful API call
      set((state) => ({
        produits: [...state.produits, response.data],
        loading: false,
        error: null
      }));
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec de l'ajout du produit" 
      });
    }
  },
  
  updateProduit: async (id, produit) => {
    try {
      set({ loading: true });
      await api.put(`/produits/${id}`, produit);
      
      // Update local state after successful API call
      set((state) => ({
        produits: state.produits.map((p) => 
          p.id === id ? { ...p, ...produit } : p
        ),
        loading: false,
        error: null
      }));
    } catch (error: any) {
      set({ 
        loading: false, 
        error: error.response?.data?.message || "Échec de la mise à jour du produit" 
      });
    }
  }
}));
