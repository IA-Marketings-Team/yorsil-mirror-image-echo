
/**
 * Routes centralisées de l'application
 * Utiliser ces constantes pour éviter les erreurs de typo dans les routes
 */

export const ROUTES = {
  // Routes d'authentification
  AUTH: {
    LOGIN: "/login",
    REGISTER: "/register",
    FORGOT_PASSWORD: "/forgot-password",
    RESET_PASSWORD: "/reset-password/:token",
  },
  
  // Routes d'administration
  ADMIN: {
    ROOT: "/admin",
    USERS: "/admin/users",
    BOUTIQUES: "/admin/boutiques",
    OPERATEURS: "/admin/operateurs",
    PAYS: "/admin/pays",
    PRODUITS: "/admin/produits",
    OFFRES: "/admin/offres",
    JOURNAL: "/admin/journal",
  },
  
  // Routes pour les boutiques
  OFFICE: {
    ROOT: "/office",
    RECHARGE: "/office/recharge",
    TRANSFERT: "/office/transfert-credit",
    BILLETERIES: "/office/billeteries",
    SERVICES: "/office/services",
    HISTORY: "/office/history",
  },
  
  // Route par défaut
  ROOT: "/",
};
