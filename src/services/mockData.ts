
/**
 * Service pour fournir des données simulées en attendant l'API
 * Ces données peuvent être facilement remplacées par des appels API réels
 */

// Types
import { User, Boutique } from "@/types";

// Utilisateurs simulés
export const mockUsers: User[] = [
  { id: 1, nom: "Dupont", prenom: "Jean", email: "jean.dupont@example.com", telephone: "+33612345678", roles: ["ROLE_ADMIN"], isActive: true },
  { id: 2, nom: "Martin", prenom: "Lucie", email: "lucie.martin@example.com", telephone: "+33612345679", roles: ["ROLE_ADMIN"], isActive: true },
  { id: 3, nom: "Bernard", prenom: "Pierre", email: "pierre.bernard@example.com", telephone: "+33612345680", roles: ["ROLE_BOUT"], isActive: true },
  { id: 4, nom: "Thomas", prenom: "Marie", email: "marie.thomas@example.com", telephone: "+33612345681", roles: ["ROLE_BOUT"], isActive: false },
  { id: 5, nom: "Petit", prenom: "Sophie", email: "sophie.petit@example.com", telephone: "+33612345682", roles: ["ROLE_USER"], isActive: true }
];

// Boutiques simulées
export const mockBoutiques: Boutique[] = [
  { id: 1, nom: "Boutique Centrale", adresse: "123 Rue de Paris", telephone: "+33612345678", email: "contact@boutiquecentrale.com", solde: 1250.75, active: true },
  { id: 2, nom: "Point Service", adresse: "45 Avenue Victor Hugo", telephone: "+33612345679", email: "contact@pointservice.com", solde: 850.25, active: true },
  { id: 3, nom: "Tabac du Coin", adresse: "12 Place de la République", telephone: "+33612345680", email: "contact@tabacducoin.com", solde: 2150.00, active: true },
  { id: 4, nom: "Boutique Express", adresse: "78 Boulevard Saint-Michel", telephone: "+33612345681", email: "contact@boutiqueexpress.com", solde: 350.50, active: false },
  { id: 5, nom: "Yorsil Services", adresse: "35 Rue des Martyrs", telephone: "+33612345682", email: "contact@yorsilservices.com", solde: 1750.30, active: true }
];

// Configuration messages
export const appConfig = {
  loadingMessages: {
    users: "Chargement des utilisateurs...",
    boutiques: "Chargement des boutiques...",
    pays: "Chargement des pays...",
    operateurs: "Chargement des opérateurs...",
    produits: "Chargement des produits...",
    recharges: "Chargement des recharges..."
  },
  errorMessages: {
    users: "Erreur de chargement des utilisateurs",
    boutiques: "Erreur de chargement des boutiques",
    pays: "Erreur de chargement des pays",
    operateurs: "Erreur de chargement des opérateurs",
    produits: "Erreur de chargement des produits",
    recharges: "Erreur de chargement des recharges"
  },
  auth: {
    passwordMinLength: 6,
    resetPasswordSuccessMessage: "Mot de passe réinitialisé avec succès!",
    resetPasswordRedirectTime: 3000, // millisecondes
    passwordResetInvalidMessage: "Échec de la réinitialisation du mot de passe. Le token peut être invalide ou expiré.",
    passwordResetMissingToken: "Token manquant. Veuillez réessayer le processus de récupération de mot de passe."
  }
};

// Fonction pour simuler un délai réseau
export const simulateNetworkDelay = async (): Promise<void> => {
  return new Promise(resolve => setTimeout(resolve, 500));
};
