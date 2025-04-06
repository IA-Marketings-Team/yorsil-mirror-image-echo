
import { api } from "./api";
import { toast } from "react-toastify";
import { User, LoginCredentials, RegisterData } from "@/types/auth.types";

export interface AuthResponse {
  token: string;
  user: User;
}

export const authService = {
  async login({ email, password }: LoginCredentials): Promise<AuthResponse> {
    try {
      const response = await api.post("/login", { email, password });
      const { token, user } = response.data;
      
      // Store token in localStorage
      localStorage.setItem("token", token);
      
      return { token, user };
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || "Login failed";
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },
  
  async register(userData: RegisterData): Promise<void> {
    try {
      await api.post("/register", userData);
      toast.success("Registration successful! You can now login.");
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || "Registration failed";
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },
  
  async getProfile(): Promise<User> {
    try {
      const response = await api.get("/user/profile");
      return response.data.user;
    } catch (error) {
      throw new Error("Failed to get user profile");
    }
  },

  async forgotPassword(email: string): Promise<void> {
    try {
      await api.post("/forgot-password", { email });
      toast.success("Password recovery email sent!");
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || "Failed to send recovery email";
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },

  async resetPassword(token: string, password: string): Promise<void> {
    try {
      await api.post(`/reset-password/${token}`, { password });
      toast.success("Password reset successful! You can now login.");
    } catch (error: any) {
      const errorMessage = error.response?.data?.message || "Password reset failed";
      toast.error(errorMessage);
      throw new Error(errorMessage);
    }
  },

  logout(): void {
    localStorage.removeItem("token");
  }
};
