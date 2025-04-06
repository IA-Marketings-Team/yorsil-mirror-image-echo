
import { useState, useEffect } from "react";
import { useNavigate, Link } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";
import { Button } from "@/components/ui/button";
import FormField from "@/components/auth/FormField";
import PasswordInput from "@/components/auth/PasswordInput";
import { Input } from "@/components/ui/input";
import { toast } from "react-toastify";

const Login = () => {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [rememberMe, setRememberMe] = useState(false);
  
  const navigate = useNavigate();
  const { login, authState } = useAuth();
  const { loading, error, user } = authState;

  useEffect(() => {
    // Redirect if user is already logged in
    if (user) {
      redirectBasedOnRole(user.roles);
    }
  }, [user, navigate]);

  const redirectBasedOnRole = (roles: string[]) => {
    if (roles.includes("ROLE_ADMIN")) {
      navigate("/admin");
    } else if (roles.includes("ROLE_BOUT")) {
      navigate("/office");
    } else {
      navigate("/");
    }
    toast.success("Connexion réussie!");
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!email || !password) {
      toast.error("Veuillez remplir tous les champs");
      return;
    }
    
    try {
      await login(email, password);
    } catch (err) {
      console.error("Erreur lors de la connexion:", err);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      <FormField id="email" label="Adresse e-mail" required>
        <Input
          id="email"
          name="email"
          type="email"
          autoComplete="email"
          required
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          placeholder="votre@email.com"
        />
      </FormField>

      <FormField id="password" label="Mot de passe" required>
        <PasswordInput
          id="password"
          name="password"
          autoComplete="current-password"
          required
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          placeholder="Votre mot de passe"
        />
      </FormField>

      <div className="flex items-center justify-between">
        <div className="flex items-center">
          <input
            id="remember-me"
            name="remember-me"
            type="checkbox"
            className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            checked={rememberMe}
            onChange={(e) => setRememberMe(e.target.checked)}
          />
          <label htmlFor="remember-me" className="ml-2 block text-sm text-gray-900">
            Se souvenir de moi
          </label>
        </div>

        <div className="text-sm">
          <Link to="/forgot-password" className="font-medium text-blue-600 hover:text-blue-500">
            Mot de passe oublié?
          </Link>
        </div>
      </div>

      <div>
        <Button
          type="submit"
          disabled={loading}
          className="w-full flex justify-center py-2 px-4"
        >
          {loading ? "Connexion..." : "Se connecter"}
        </Button>
      </div>

      {error && (
        <div className="text-red-500 text-sm mt-2">
          {error}
        </div>
      )}
      
      <div className="text-center text-sm text-gray-600">
        <p>Pas encore de compte? <Link to="/register" className="text-blue-600 hover:text-blue-500">Créer un compte</Link></p>
      </div>
    </form>
  );
};

export default Login;
