
export interface User {
  id: number;
  email: string;
  nom: string;
  prenom?: string;
  roles: string[];
  picture?: string;
  sessionToken?: string;
}

export interface Boutique {
  id: number;
  nom: string;
  adresse?: string;
  telephone?: string;
  user: User;
  isActive: boolean;
}

export interface Pays {
  id: number;
  nom: string;
  code: string;
  isApi: boolean;
  typeApi?: string;
}

export interface Operateur {
  id: number;
  nom: string;
  type: string;
  logo?: string;
  idPays: Pays;
  longueurCode?: number;
  actif: boolean;
  nomPays: string;
}

export interface Produit {
  id: number;
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
  id: number;
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
  id: number;
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
  id: number;
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
