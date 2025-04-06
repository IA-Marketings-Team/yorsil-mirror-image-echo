
export interface User {
  id: string; // Changé de number à string pour correspondre au format UUID de Supabase
  email: string;
  nom: string;
  prenom?: string;
  roles: Role[];
  picture?: string;
  sessionToken?: string;
}

export type Role = "ROLE_ADMIN" | "ROLE_BOUT" | "ROLE_USER" | "ROLE_PERC";

export interface AuthState {
  user: User | null;
  token: string | null;
  loading: boolean;
  error: string | null;
}

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  nom: string;
  prenom?: string;
  email: string;
  password: string;
  telephone?: string;
}

export interface AuthContextType {
  authState: AuthState;
  login: (email: string, password: string) => Promise<void>;
  logout: () => void;
  register: (userData: RegisterData) => Promise<void>;
  forgotPassword: (email: string) => Promise<void>;
  resetPassword: (token: string, password: string) => Promise<void>;
}
