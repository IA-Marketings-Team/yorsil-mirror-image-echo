import { useState } from "react";
import { useParams, useNavigate, Link } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";
import { Button } from "@/components/ui/button";
import FormField from "@/components/auth/FormField";
import PasswordInput from "@/components/auth/PasswordInput";

const ResetPassword = () => {
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [error, setError] = useState("");
  const [success, setSuccess] = useState(false);
  
  const { token } = useParams<{ token: string }>();
  const navigate = useNavigate();
  
  const { resetPassword, authState } = useAuth();
  const { loading } = authState;

  const validateForm = () => {
    if (password.length < 6) {
      setError("Le mot de passe doit contenir au moins 6 caractères");
      return false;
    }
    
    if (password !== confirmPassword) {
      setError("Les mots de passe ne correspondent pas");
      return false;
    }
    
    setError("");
    return true;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!validateForm()) return;
    
    if (token) {
      try {
        await resetPassword(token, password);
        setSuccess(true);
        // Redirect to login after a short delay
        setTimeout(() => {
          navigate("/login");
        }, 3000);
      } catch (err) {
        setError("Échec de la réinitialisation du mot de passe. Le token peut être invalide ou expiré.");
      }
    } else {
      setError("Token manquant. Veuillez réessayer le processus de récupération de mot de passe.");
    }
  };

  return (
    <div>
      {success ? (
        <div className="text-center">
          <div className="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg
              className="h-6 w-6 text-green-600"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth="2"
                d="M5 13l4 4L19 7"
              ></path>
            </svg>
          </div>
          <h3 className="mt-2 text-sm font-medium text-gray-900">Mot de passe réinitialisé avec succès!</h3>
          <p className="mt-1 text-sm text-gray-500">
            Vous allez être redirigé vers la page de connexion dans quelques instants.
          </p>
          <div className="mt-6">
            <Button
              asChild
              variant="default"
              className="inline-flex items-center px-4 py-2"
            >
              <Link to="/login">Aller à la connexion</Link>
            </Button>
          </div>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="text-center">
            <h1 className="text-xl font-semibold">Réinitialisation du mot de passe</h1>
            <p className="mt-2 text-sm text-gray-600">
              Veuillez entrer votre nouveau mot de passe.
            </p>
          </div>

          {error && (
            <div className="bg-red-50 border-l-4 border-red-500 p-4">
              <p className="text-sm text-red-700">{error}</p>
            </div>
          )}

          <FormField id="password" label="Nouveau mot de passe" required>
            <PasswordInput
              id="password"
              name="password"
              autoComplete="new-password"
              required
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="Minimum 6 caractères"
            />
          </FormField>

          <FormField id="confirmPassword" label="Confirmer le mot de passe" required>
            <PasswordInput
              id="confirmPassword"
              name="confirmPassword"
              autoComplete="new-password"
              required
              value={confirmPassword}
              onChange={(e) => setConfirmPassword(e.target.value)}
              placeholder="Confirmer votre mot de passe"
            />
          </FormField>

          <div>
            <Button
              type="submit"
              disabled={loading}
              className="w-full"
            >
              {loading ? "Réinitialisation en cours..." : "Réinitialiser le mot de passe"}
            </Button>
          </div>

          <div className="text-sm text-center">
            <Link to="/login" className="font-medium text-blue-600 hover:text-blue-500">
              Retour à la connexion
            </Link>
          </div>
        </form>
      )}
    </div>
  );
};

export default ResetPassword;
