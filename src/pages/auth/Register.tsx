
import { useState } from "react";
import { Link } from "react-router-dom";
import { useAuth } from "@/contexts/AuthContext";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Eye, EyeOff } from "lucide-react";

const Register = () => {
  const [formData, setFormData] = useState({
    nom: "",
    prenom: "",
    email: "",
    password: "",
    confirmPassword: "",
    telephone: ""
  });
  const [showPassword, setShowPassword] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  
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
        <div>
          <label htmlFor="nom" className="block text-sm font-medium text-gray-700">
            Nom <span className="text-red-500">*</span>
          </label>
          <Input
            id="nom"
            name="nom"
            type="text"
            required
            value={formData.nom}
            onChange={handleChange}
            className={errors.nom ? "border-red-500" : ""}
          />
          {errors.nom && <p className="mt-1 text-xs text-red-500">{errors.nom}</p>}
        </div>

        <div>
          <label htmlFor="prenom" className="block text-sm font-medium text-gray-700">
            Prénom
          </label>
          <Input
            id="prenom"
            name="prenom"
            type="text"
            value={formData.prenom}
            onChange={handleChange}
          />
        </div>
      </div>

      <div>
        <label htmlFor="email" className="block text-sm font-medium text-gray-700">
          Email <span className="text-red-500">*</span>
        </label>
        <Input
          id="email"
          name="email"
          type="email"
          autoComplete="email"
          required
          value={formData.email}
          onChange={handleChange}
          className={errors.email ? "border-red-500" : ""}
        />
        {errors.email && <p className="mt-1 text-xs text-red-500">{errors.email}</p>}
      </div>

      <div>
        <label htmlFor="telephone" className="block text-sm font-medium text-gray-700">
          Téléphone
        </label>
        <Input
          id="telephone"
          name="telephone"
          type="tel"
          value={formData.telephone}
          onChange={handleChange}
        />
      </div>

      <div>
        <label htmlFor="password" className="block text-sm font-medium text-gray-700">
          Mot de passe <span className="text-red-500">*</span>
        </label>
        <div className="relative">
          <Input
            id="password"
            name="password"
            type={showPassword ? "text" : "password"}
            autoComplete="new-password"
            required
            value={formData.password}
            onChange={handleChange}
            className={errors.password ? "border-red-500" : ""}
          />
          <button
            type="button"
            onClick={() => setShowPassword(!showPassword)}
            className="absolute right-3 top-1/2 transform -translate-y-1/2"
          >
            {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
          </button>
        </div>
        {errors.password && <p className="mt-1 text-xs text-red-500">{errors.password}</p>}
      </div>

      <div>
        <label htmlFor="confirmPassword" className="block text-sm font-medium text-gray-700">
          Confirmer le mot de passe <span className="text-red-500">*</span>
        </label>
        <div className="relative">
          <Input
            id="confirmPassword"
            name="confirmPassword"
            type={showPassword ? "text" : "password"}
            autoComplete="new-password"
            required
            value={formData.confirmPassword}
            onChange={handleChange}
            className={errors.confirmPassword ? "border-red-500" : ""}
          />
          <button
            type="button"
            onClick={() => setShowPassword(!showPassword)}
            className="absolute right-3 top-1/2 transform -translate-y-1/2"
          >
            {showPassword ? <EyeOff size={20} /> : <Eye size={20} />}
          </button>
        </div>
        {errors.confirmPassword && (
          <p className="mt-1 text-xs text-red-500">{errors.confirmPassword}</p>
        )}
      </div>

      <div className="flex items-center">
        <input
          id="agree-terms"
          name="agree-terms"
          type="checkbox"
          required
          className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
        />
        <label htmlFor="agree-terms" className="ml-2 block text-sm text-gray-900">
          J'accepte les <a href="#" className="text-blue-600 hover:underline">conditions générales d'utilisation</a>
        </label>
      </div>

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
