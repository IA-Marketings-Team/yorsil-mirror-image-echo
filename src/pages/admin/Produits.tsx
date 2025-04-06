import { useState } from "react";
import { useQuery, useMutation, useQueryClient } from "@tanstack/react-query";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
  DialogFooter,
} from "@/components/ui/dialog";
import {
  Table,
  TableBody,
  TableCaption,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Plus, PackagePlus, Edit, Trash2 } from "lucide-react";
import { api } from "@/services/api";
import { Produit } from "@/types/entities.types";
import PageHeader from "@/components/common/PageHeader";
import { toast } from "react-toastify";

const AdminProduits = () => {
  const [showAddModal, setShowAddModal] = useState(false);
  const [showEditModal, setShowEditModal] = useState(false);
  const [selectedProduit, setSelectedProduit] = useState<Produit | null>(null);
  const queryClient = useQueryClient();

  // Fetch produits
  const { data: produits, isLoading } = useQuery({
    queryKey: ["produits"],
    queryFn: async () => {
      const response = await api.get<Produit[]>("/produits");
      return response.data;
    },
  });

  // Add produit mutation
  const addProduitMutation = useMutation(
    async (newProduit: Omit<Produit, "id">) => {
      const response = await api.post<Produit>("/produits", newProduit);
      return response.data;
    },
    {
      onSuccess: () => {
        queryClient.invalidateQueries(["produits"]);
        setShowAddModal(false);
        toast.success("Produit ajouté avec succès!");
      },
      onError: (error: any) => {
        toast.error(
          error?.response?.data?.message ||
            "Erreur lors de l'ajout du produit."
        );
      },
    }
  );

  // Edit produit mutation
  const editProduitMutation = useMutation(
    async (updatedProduit: Produit) => {
      const response = await api.put<Produit>(
        `/produits/${updatedProduit.id}`,
        updatedProduit
      );
      return response.data;
    },
    {
      onSuccess: () => {
        queryClient.invalidateQueries(["produits"]);
        setShowEditModal(false);
        setSelectedProduit(null);
        toast.success("Produit mis à jour avec succès!");
      },
      onError: (error: any) => {
        toast.error(
          error?.response?.data?.message ||
            "Erreur lors de la modification du produit."
        );
      },
    }
  );

  // Delete produit mutation
  const deleteProduitMutation = useMutation(
    async (id: number) => {
      await api.delete(`/produits/${id}`);
    },
    {
      onSuccess: () => {
        queryClient.invalidateQueries(["produits"]);
        toast.success("Produit supprimé avec succès!");
      },
      onError: (error: any) => {
        toast.error(
          error?.response?.data?.message ||
            "Erreur lors de la suppression du produit."
        );
      },
    }
  );

  const handleEdit = (produit: Produit) => {
    setSelectedProduit(produit);
    setShowEditModal(true);
  };

  const handleDelete = (id: number) => {
    if (window.confirm("Êtes-vous sûr de vouloir supprimer ce produit?")) {
      deleteProduitMutation.mutate(id);
    }
  };

  return (
    <div className="space-y-6">
      <PageHeader 
        title="Produits"
        actions={
          <Button onClick={() => setShowAddModal(true)} className="flex items-center gap-2">
            <PackagePlus size={16} />
            Ajouter un produit
          </Button>
        }
      />

      <Table>
        <TableCaption>Liste des produits enregistrés.</TableCaption>
        <TableHeader>
          <TableRow>
            <TableHead className="w-[100px]">ID</TableHead>
            <TableHead>Nom</TableHead>
            <TableHead>Description</TableHead>
            <TableHead>Catégorie</TableHead>
            <TableHead>Prix</TableHead>
            <TableHead>Stock</TableHead>
            <TableHead>Actif</TableHead>
            <TableHead className="text-right">Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          {isLoading ? (
            <TableRow>
              <TableCell colSpan={8} className="text-center">
                Chargement...
              </TableCell>
            </TableRow>
          ) : produits && produits.length > 0 ? (
            produits.map((produit) => (
              <TableRow key={produit.id}>
                <TableCell className="font-medium">{produit.id}</TableCell>
                <TableCell>{produit.nom}</TableCell>
                <TableCell>{produit.description}</TableCell>
                <TableCell>{produit.categorie}</TableCell>
                <TableCell>{produit.prix}</TableCell>
                <TableCell>{produit.stock}</TableCell>
                <TableCell>{produit.actif ? "Oui" : "Non"}</TableCell>
                <TableCell className="text-right">
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => handleEdit(produit)}
                  >
                    <Edit className="mr-2 h-4 w-4" />
                    Modifier
                  </Button>
                  <Button
                    variant="ghost"
                    size="sm"
                    onClick={() => handleDelete(produit.id)}
                  >
                    <Trash2 className="mr-2 h-4 w-4" />
                    Supprimer
                  </Button>
                </TableCell>
              </TableRow>
            ))
          ) : (
            <TableRow>
              <TableCell colSpan={8} className="text-center">
                Aucun produit trouvé.
              </TableCell>
            </TableRow>
          )}
        </TableBody>
      </Table>

      {/* Add Produit Modal */}
      <Dialog open={showAddModal} onOpenChange={setShowAddModal}>
        <DialogContent className="sm:max-w-[425px]">
          <DialogHeader>
            <DialogTitle>Ajouter un produit</DialogTitle>
            <DialogDescription>
              Ajouter un nouveau produit à la liste.
            </DialogDescription>
          </DialogHeader>
          <AddEditProduitForm
            onSubmit={async (values) => {
              await addProduitMutation.mutateAsync(values);
            }}
            onCancel={() => setShowAddModal(false)}
            loading={addProduitMutation.isLoading}
          />
        </DialogContent>
      </Dialog>

      {/* Edit Produit Modal */}
      <Dialog open={showEditModal} onOpenChange={setShowEditModal}>
        <DialogContent className="sm:max-w-[425px]">
          <DialogHeader>
            <DialogTitle>Modifier un produit</DialogTitle>
            <DialogDescription>
              Modifier les informations du produit.
            </DialogDescription>
          </DialogHeader>
          <AddEditProduitForm
            produit={selectedProduit || undefined}
            onSubmit={async (values) => {
              if (selectedProduit) {
                await editProduitMutation.mutateAsync({
                  ...selectedProduit,
                  ...values,
                });
              }
            }}
            onCancel={() => {
              setShowEditModal(false);
              setSelectedProduit(null);
            }}
            loading={editProduitMutation.isLoading}
          />
        </DialogContent>
      </Dialog>
    </div>
  );
};

interface AddEditProduitFormProps {
  onSubmit: (values: Omit<Produit, "id">) => Promise<void>;
  onCancel: () => void;
  loading: boolean;
  produit?: Produit;
}

const AddEditProduitForm: React.FC<AddEditProduitFormProps> = ({
  onSubmit,
  onCancel,
  loading,
  produit,
}) => {
  const [nom, setNom] = useState(produit?.nom || "");
  const [description, setDescription] = useState(produit?.description || "");
  const [categorie, setCategorie] = useState(produit?.categorie || "");
  const [prix, setPrix] = useState(produit?.prix || 0);
  const [stock, setStock] = useState(produit?.stock || 0);
  const [actif, setActif] = useState(produit?.actif || false);

  const handleSubmit = async (event: React.FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    const values = {
      nom,
      description,
      categorie,
      prix: Number(prix),
      stock: Number(stock),
      actif,
    };
    await onSubmit(values);
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <div>
        <Label htmlFor="nom">Nom</Label>
        <Input
          id="nom"
          value={nom}
          onChange={(e) => setNom(e.target.value)}
          required
        />
      </div>
      <div>
        <Label htmlFor="description">Description</Label>
        <Input
          id="description"
          value={description}
          onChange={(e) => setDescription(e.target.value)}
        />
      </div>
      <div>
        <Label htmlFor="categorie">Catégorie</Label>
        <Input
          id="categorie"
          value={categorie}
          onChange={(e) => setCategorie(e.target.value)}
          required
        />
      </div>
      <div>
        <Label htmlFor="prix">Prix</Label>
        <Input
          type="number"
          id="prix"
          value={prix}
          onChange={(e) => setPrix(e.target.value)}
          required
        />
      </div>
      <div>
        <Label htmlFor="stock">Stock</Label>
        <Input
          type="number"
          id="stock"
          value={stock}
          onChange={(e) => setStock(e.target.value)}
          required
        />
      </div>
      <div>
        <Label htmlFor="actif">Actif</Label>
        <Input
          type="checkbox"
          id="actif"
          checked={actif}
          onChange={(e) => setActif(e.target.checked)}
        />
      </div>
      <DialogFooter>
        <Button type="button" variant="secondary" onClick={onCancel}>
          Annuler
        </Button>
        <Button type="submit" disabled={loading}>
          {loading ? "Enregistrement..." : "Enregistrer"}
        </Button>
      </DialogFooter>
    </form>
  );
};

export default AdminProduits;
