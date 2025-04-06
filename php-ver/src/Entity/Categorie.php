<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="categorie_id")
     */
    private $produits;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=ProduitPhysique::class, mappedBy="categorie")
     */
    private $produitPhysiques;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->produitPhysiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setCategorieId($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getCategorieId() === $this) {
                $produit->setCategorieId(null);
            }
        }

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|ProduitPhysique[]
     */
    public function getProduitPhysiques(): Collection
    {
        return $this->produitPhysiques;
    }

    public function addProduitPhysique(ProduitPhysique $produitPhysique): self
    {
        if (!$this->produitPhysiques->contains($produitPhysique)) {
            $this->produitPhysiques[] = $produitPhysique;
            $produitPhysique->setCategorie($this);
        }

        return $this;
    }

    public function removeProduitPhysique(ProduitPhysique $produitPhysique): self
    {
        if ($this->produitPhysiques->removeElement($produitPhysique)) {
            // set the owning side to null (unless already changed)
            if ($produitPhysique->getCategorie() === $this) {
                $produitPhysique->setCategorie(null);
            }
        }

        return $this;
    }
}
