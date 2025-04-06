
import { Database } from "@/integrations/supabase/types";

// Types extraits de la base de données Supabase pour faciliter l'utilisation
export type Tables = Database['public']['Tables'];
export type TablesInsert = Database['public']['Tables'];
export type TablesUpdate = Database['public']['Tables'];

// Typages pour chaque table
export type UserRow = Tables['users']['Row'];
export type UserInsert = TablesInsert['users']['Insert'];
export type UserUpdate = TablesUpdate['users']['Update'];

export type BoutiqueRow = Tables['boutiques']['Row'];
export type BoutiqueInsert = TablesInsert['boutiques']['Insert'];
export type BoutiqueUpdate = TablesUpdate['boutiques']['Update'];

export type PaysRow = Tables['pays']['Row'];
export type PaysInsert = TablesInsert['pays']['Insert'];
export type PaysUpdate = TablesUpdate['pays']['Update'];

export type OperateurRow = Tables['operateurs']['Row'];
export type OperateurInsert = TablesInsert['operateurs']['Insert'];
export type OperateurUpdate = TablesUpdate['operateurs']['Update'];

export type ProduitRow = Tables['produits']['Row'];
export type ProduitInsert = TablesInsert['produits']['Insert'];
export type ProduitUpdate = TablesUpdate['produits']['Update'];

export type RechargeRow = Tables['recharges']['Row'];
export type RechargeInsert = TablesInsert['recharges']['Insert'];
export type RechargeUpdate = TablesUpdate['recharges']['Update'];

export type RechargeFlexiRow = Tables['recharges_flexi']['Row'];
export type RechargeFlexiInsert = TablesInsert['recharges_flexi']['Insert'];
export type RechargeFlexiUpdate = TablesUpdate['recharges_flexi']['Update'];

export type NotificationRow = Tables['notifications']['Row'];
export type NotificationInsert = TablesInsert['notifications']['Insert'];
export type NotificationUpdate = TablesUpdate['notifications']['Update'];
