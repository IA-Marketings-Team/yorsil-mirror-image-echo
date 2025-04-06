
export interface User {
  id: string; // Changed from number to string to match Supabase UUID
  email: string;
  nom: string;
  prenom?: string;
  roles: string[];
  picture?: string;
  sessionToken?: string;
}

export interface Boutique {
  id: string; // Changed from number to string to match Supabase UUID
  nom: string;
  adresse?: string;
  telephone?: string;
  user: User;
  isActive: boolean;
  solde?: number; // Added as optional since it might not be in Supabase
  active?: boolean; // Added as optional to match existing code
}

export interface Pays {
  id: string; // Changed from number to string to match Supabase UUID
  nom: string;
  code: string;
  isApi: boolean;
  typeApi?: string;
}

export interface Operateur {
  id: string; // Changed from number to string to match Supabase UUID
  nom: string;
  type: string;
  logo?: string;
  idPays: Pays;
  longueurCode?: number;
  actif: boolean;
  nomPays: string;
}

export interface Produit {
  id: string; // Changed from number to string to match Supabase UUID
  nom: string;
  prixAchat: number;
  prixVente: number;
  operateur: Operateur;
  type?: string;
  gencode?: boolean;
  description?: string;
  instruction?: string;
  isVisible?: boolean;
  isProductNew?: boolean;
  categorie?: string;
}

export interface Recharge {
  id: string; // Changed from number to string to match Supabase UUID
  articles: any[];
  saleRef: string;
  internalRef: string;
  montant: number;
  processState: string;
  saleDate: string;
  productInformations: any[];
  qty: number;
  voucher: any[];
  boutique: Boutique;
  tva?: number;
  frais?: number;
  frais_boutique?: number;
}

export interface RechargeFlexi {
  id: string; // Changed from number to string to match Supabase UUID
  numero: string;
  nomoffre: string;
  montant: number;
  date: string;
  isvalid: boolean;
  operateur: Operateur;
  user: User;
  frais_bout?: number;
  frais?: number;
}

export interface Notification {
  id: string; // Changed from number to string to match Supabase UUID
  message: string;
  status: string;
  createdAt: string;
  user?: User;
}

export interface AuthState {
  user: User | null;
  token: string | null;
  loading: boolean;
  error: string | null;
}

export interface AuthContextType {
  authState: AuthState;
  login: (email: string, password: string) => Promise<void>;
  logout: () => void;
  register: (userData: any) => Promise<void>;
  forgotPassword: (email: string) => Promise<void>;
  resetPassword: (token: string, password: string) => Promise<void>;
}

export type Role = "ROLE_ADMIN" | "ROLE_BOUT" | "ROLE_USER" | "ROLE_PERC";
