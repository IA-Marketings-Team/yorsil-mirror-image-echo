
import { useState } from "react";
import { Link } from "react-router-dom";
import { useAuth } from "@/hooks/useAuth";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import FormField from "@/components/auth/FormField";
import PasswordInput from "@/components/auth/PasswordInput";

const Register = () => {
  const [formData, setFormData] = useState({
    nom: "",
    prenom: "",
    email: "",
    password: "",
    confirmPassword: "",
    telephone: ""
  });
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [agreeTerms, setAgreeTerms] = useState(false);
  
  const { register, authState } = useAuth();
  const { loading } = authState;

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
    // Clear error when user starts typing
    if (errors[name]) {
      setErrors({ ...errors, [name]: "" });
    }
  };

  const validateForm = () => {
    const newErrors: Record<string, string> = {};
    
    if (!formData.nom.trim()) {
      newErrors.nom = "Le nom est requis";
    }
    
    if (!formData.email.trim()) {
      newErrors.email = "L'email est requis";
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = "Format d'email invalide";
    }
    
    if (!formData.password) {
      newErrors.password = "Le mot de passe est requis";
    } else if (formData.password.length < 6) {
      newErrors.password = "Le mot de passe doit contenir au moins 6 caractères";
    }
    
    if (formData.password !== formData.confirmPassword) {
      newErrors.confirmPassword = "Les mots de passe ne correspondent pas";
    }
    
    if (!agreeTerms) {
      newErrors.agreeTerms = "Vous devez accepter les conditions d'utilisation";
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (validateForm()) {
      const userData = {
        nom: formData.nom,
        prenom: formData.prenom,
        email: formData.email,
        password: formData.password,
        telephone: formData.telephone
      };
      
      await register(userData);
    }
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <FormField id="nom" label="Nom" required error={errors.nom}>
          <Input
            id="nom"
            name="nom"
            type="text"
            required
            value={formData.nom}
            onChange={handleChange}
          />
        </FormField>

        <FormField id="prenom" label="Prénom">
          <Input
            id="prenom"
            name="prenom"
            type="text"
            value={formData.prenom}
            onChange={handleChange}
          />
        </FormField>
      </div>

      <FormField id="email" label="Email" required error={errors.email}>
        <Input
          id="email"
          name="email"
          type="email"
          autoComplete="email"
          required
          value={formData.email}
          onChange={handleChange}
        />
      </FormField>

      <FormField id="telephone" label="Téléphone">
        <Input
          id="telephone"
          name="telephone"
          type="tel"
          value={formData.telephone}
          onChange={handleChange}
        />
      </FormField>

      <FormField id="password" label="Mot de passe" required error={errors.password}>
        <PasswordInput
          id="password"
          name="password"
          autoComplete="new-password"
          required
          value={formData.password}
          onChange={handleChange}
        />
      </FormField>

      <FormField id="confirmPassword" label="Confirmer le mot de passe" required error={errors.confirmPassword}>
        <PasswordInput
          id="confirmPassword"
          name="confirmPassword"
          autoComplete="new-password"
          required
          value={formData.confirmPassword}
          onChange={handleChange}
        />
      </FormField>

      <div className="flex items-center">
        <input
          id="agree-terms"
          name="agree-terms"
          type="checkbox"
          checked={agreeTerms}
          onChange={(e) => setAgreeTerms(e.target.checked)}
          className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        />
        <label htmlFor="agree-terms" className="ml-2 block text-sm text-gray-900">
          J'accepte les <a href="#" className="text-blue-600 hover:underline">conditions générales d'utilisation</a>
        </label>
      </div>
      {errors.agreeTerms && <p className="text-xs text-red-500">{errors.agreeTerms}</p>}

      <div>
        <Button
          type="submit"
          disabled={loading}
          className="w-full"
        >
          {loading ? "Création en cours..." : "Créer un compte"}
        </Button>
      </div>

      <div className="text-sm text-center">
        Vous avez déjà un compte?{" "}
        <Link to="/login" className="font-medium text-blue-600 hover:text-blue-500">
          Connectez-vous
        </Link>
      </div>
    </form>
  );
};

export default Register;
