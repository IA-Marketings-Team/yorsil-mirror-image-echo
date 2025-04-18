
import { useState } from "react";
import { Link } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import FormField from "@/components/auth/FormField";

const ForgotPassword = () => {
  const [email, setEmail] = useState("");
  const [submitted, setSubmitted] = useState(false);
  const { forgotPassword, authState } = useAuth();
  const { loading } = authState;

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    await forgotPassword(email);
    setSubmitted(true);
  };

  return (
    <div>
      {submitted ? (
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
          <h3 className="mt-2 text-sm font-medium text-gray-900">Email envoyé!</h3>
          <p className="mt-1 text-sm text-gray-500">
            Si un compte existe avec cette adresse email, vous recevrez un lien pour réinitialiser votre mot de passe.
          </p>
          <div className="mt-6">
            <Button
              asChild
              variant="default"
              className="inline-flex items-center px-4 py-2"
            >
              <Link to="/login">Retour à la connexion</Link>
            </Button>
          </div>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="text-center">
            <h1 className="text-xl font-semibold">Mot de passe oublié?</h1>
            <p className="mt-2 text-sm text-gray-600">
              Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>
          </div>

          <FormField id="email" label="Adresse email" required>
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

          <div>
            <Button
              type="submit"
              disabled={loading}
              className="w-full"
            >
              {loading ? "Envoi en cours..." : "Envoyer le lien de réinitialisation"}
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

export default ForgotPassword;
