
import { supabase } from "@/integrations/supabase/client";
import { Boutique, User, Pays, Operateur, Produit, Recharge, RechargeFlexi, Notification } from "@/types";

export const supabaseService = {
  // Services pour les utilisateurs
  users: {
    async getAll() {
      const { data, error } = await supabase
        .from('users')
        .select('*');
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getById(id: string) {
      const { data, error } = await supabase
        .from('users')
        .select('*')
        .eq('id', id)
        .single();
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async create(user: Partial<User>) {
      const { data, error } = await supabase
        .from('users')
        .insert(user)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async update(id: string, user: Partial<User>) {
      const { data, error } = await supabase
        .from('users')
        .update(user)
        .eq('id', id)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async delete(id: string) {
      const { error } = await supabase
        .from('users')
        .delete()
        .eq('id', id);
      
      if (error) throw new Error(error.message);
      return true;
    }
  },
  
  // Services pour les boutiques
  boutiques: {
    async getAll() {
      const { data, error } = await supabase
        .from('boutiques')
        .select('*, user:user_id(*)');
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getById(id: string) {
      const { data, error } = await supabase
        .from('boutiques')
        .select('*, user:user_id(*)')
        .eq('id', id)
        .single();
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getByUserId(userId: string) {
      const { data, error } = await supabase
        .from('boutiques')
        .select('*, user:user_id(*)')
        .eq('user_id', userId);
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async create(boutique: Partial<Boutique>) {
      const { data, error } = await supabase
        .from('boutiques')
        .insert(boutique)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async update(id: string, boutique: Partial<Boutique>) {
      const { data, error } = await supabase
        .from('boutiques')
        .update(boutique)
        .eq('id', id)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async delete(id: string) {
      const { error } = await supabase
        .from('boutiques')
        .delete()
        .eq('id', id);
      
      if (error) throw new Error(error.message);
      return true;
    }
  },
  
  // Services pour les pays
  pays: {
    async getAll() {
      const { data, error } = await supabase
        .from('pays')
        .select('*');
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getById(id: string) {
      const { data, error } = await supabase
        .from('pays')
        .select('*')
        .eq('id', id)
        .single();
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async create(pays: Partial<Pays>) {
      const { data, error } = await supabase
        .from('pays')
        .insert(pays)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async update(id: string, pays: Partial<Pays>) {
      const { data, error } = await supabase
        .from('pays')
        .update(pays)
        .eq('id', id)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async delete(id: string) {
      const { error } = await supabase
        .from('pays')
        .delete()
        .eq('id', id);
      
      if (error) throw new Error(error.message);
      return true;
    }
  },
  
  // Services pour les opérateurs
  operateurs: {
    async getAll() {
      const { data, error } = await supabase
        .from('operateurs')
        .select('*, pays:pays_id(*)');
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getById(id: string) {
      const { data, error } = await supabase
        .from('operateurs')
        .select('*, pays:pays_id(*)')
        .eq('id', id)
        .single();
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async create(operateur: Partial<Operateur>) {
      const { data, error } = await supabase
        .from('operateurs')
        .insert(operateur)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async update(id: string, operateur: Partial<Operateur>) {
      const { data, error } = await supabase
        .from('operateurs')
        .update(operateur)
        .eq('id', id)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async delete(id: string) {
      const { error } = await supabase
        .from('operateurs')
        .delete()
        .eq('id', id);
      
      if (error) throw new Error(error.message);
      return true;
    }
  },
  
  // Services pour les produits
  produits: {
    async getAll() {
      const { data, error } = await supabase
        .from('produits')
        .select('*, operateur:operateur_id(*)');
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getById(id: string) {
      const { data, error } = await supabase
        .from('produits')
        .select('*, operateur:operateur_id(*)')
        .eq('id', id)
        .single();
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async create(produit: Partial<Produit>) {
      const { data, error } = await supabase
        .from('produits')
        .insert(produit)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async update(id: string, produit: Partial<Produit>) {
      const { data, error } = await supabase
        .from('produits')
        .update(produit)
        .eq('id', id)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async delete(id: string) {
      const { error } = await supabase
        .from('produits')
        .delete()
        .eq('id', id);
      
      if (error) throw new Error(error.message);
      return true;
    }
  },
  
  // Services pour les recharges
  recharges: {
    async getAll() {
      const { data, error } = await supabase
        .from('recharges')
        .select('*, boutique:boutique_id(*)');
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getById(id: string) {
      const { data, error } = await supabase
        .from('recharges')
        .select('*, boutique:boutique_id(*)')
        .eq('id', id)
        .single();
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getByBoutiqueId(boutiqueId: string) {
      const { data, error } = await supabase
        .from('recharges')
        .select('*, boutique:boutique_id(*)')
        .eq('boutique_id', boutiqueId);
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async create(recharge: Partial<Recharge>) {
      const { data, error } = await supabase
        .from('recharges')
        .insert(recharge)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async update(id: string, recharge: Partial<Recharge>) {
      const { data, error } = await supabase
        .from('recharges')
        .update(recharge)
        .eq('id', id)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    }
  },
  
  // Services pour les recharges flexi
  rechargesFlexi: {
    async getAll() {
      const { data, error } = await supabase
        .from('recharges_flexi')
        .select('*, operateur:operateur_id(*), user:user_id(*)');
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getById(id: string) {
      const { data, error } = await supabase
        .from('recharges_flexi')
        .select('*, operateur:operateur_id(*), user:user_id(*)')
        .eq('id', id)
        .single();
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getByUserId(userId: string) {
      const { data, error } = await supabase
        .from('recharges_flexi')
        .select('*, operateur:operateur_id(*), user:user_id(*)')
        .eq('user_id', userId);
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async create(rechargeFlexi: Partial<RechargeFlexi>) {
      const { data, error } = await supabase
        .from('recharges_flexi')
        .insert(rechargeFlexi)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async update(id: string, rechargeFlexi: Partial<RechargeFlexi>) {
      const { data, error } = await supabase
        .from('recharges_flexi')
        .update(rechargeFlexi)
        .eq('id', id)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    }
  },
  
  // Services pour les notifications
  notifications: {
    async getAll() {
      const { data, error } = await supabase
        .from('notifications')
        .select('*, user:user_id(*)');
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async getByUserId(userId: string) {
      const { data, error } = await supabase
        .from('notifications')
        .select('*, user:user_id(*)')
        .eq('user_id', userId);
      
      if (error) throw new Error(error.message);
      return data;
    },
    
    async create(notification: Partial<Notification>) {
      const { data, error } = await supabase
        .from('notifications')
        .insert(notification)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    },
    
    async markAsRead(id: string) {
      const { data, error } = await supabase
        .from('notifications')
        .update({ status: 'read' })
        .eq('id', id)
        .select();
      
      if (error) throw new Error(error.message);
      return data[0];
    }
  }
};
