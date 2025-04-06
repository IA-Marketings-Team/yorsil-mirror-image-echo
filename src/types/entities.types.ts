
import { User } from "./auth.types";

export interface Boutique {
  id: number;
  nom: string;
  adresse?: string;
  telephone?: string;
  email: string;
  solde: number;
  active: boolean;
}

export interface Produit {
  id: number;
  nom: string;
  description: string;
  categorie: string;
  prix: number;
  stock: number;
  actif: boolean;
}
