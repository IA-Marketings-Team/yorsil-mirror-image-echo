<?php

namespace App\Entity;

use App\Repository\ProduitPhysiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitPhysiqueRepository::class)
 */
class ProduitPhysique
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $nom;

    /**
     * @ORM\Column(type="float")
     */
    private $prixAchat;

    /**
     * @ORM\Column(type="float")
     */
    private $prixVente;

    /**
     * @ORM\Column(type="string", length=50,nullable=true)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity=Operateur::class, inversedBy="produitPhysiques")
     */
    private $operateur;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $gencode;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=ProduitPhysiqueCode::class, mappedBy="produit_physique")
     */
    private $produitPhysiqueCodes;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_visible;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_product_new;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="produitPhysiques")
     */
    private $categorie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $instruction;

    public function __construct()
    {
        $this->produitPhysiqueCodes = new ArrayCollection();
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

    public function getPrixAchat(): ?float
    {
        return $this->prixAchat;
    }

    public function setPrixAchat(float $prixAchat): self
    {
        $this->prixAchat = $prixAchat;

        return $this;
    }

    public function getPrixVente(): ?float
    {
        return $this->prixVente;
    }

    public function setPrixVente(float $prixVente): self
    {
        $this->prixVente = $prixVente;

        return $this;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getOperateur(): ?operateur
    {
        return $this->operateur;
    }

    public function setOperateur(?operateur $operateur): self
    {
        $this->operateur = $operateur;

        return $this;
    }

    public function getGencode(): ?string
    {
        return $this->gencode;
    }

    public function setGencode(string $gencode): self
    {
        $this->gencode = $gencode;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|ProduitPhysiqueCode[]
     */
    public function getProduitPhysiqueCodes(): Collection
    {
        return $this->produitPhysiqueCodes;
    }

    public function addProduitPhysiqueCode(ProduitPhysiqueCode $produitPhysiqueCode): self
    {
        if (!$this->produitPhysiqueCodes->contains($produitPhysiqueCode)) {
            $this->produitPhysiqueCodes[] = $produitPhysiqueCode;
            $produitPhysiqueCode->setProduitPhysique($this);
        }

        return $this;
    }

    public function removeProduitPhysiqueCode(ProduitPhysiqueCode $produitPhysiqueCode): self
    {
        if ($this->produitPhysiqueCodes->removeElement($produitPhysiqueCode)) {
            // set the owning side to null (unless already changed)
            if ($produitPhysiqueCode->getProduitPhysique() === $this) {
                $produitPhysiqueCode->setProduitPhysique(null);
            }
        }

        return $this;
    }

    public function getIsVisible(): ?bool
    {
        return $this->is_visible;
    }

    public function setIsVisible(bool $is_visible): self
    {
        $this->is_visible = $is_visible;

        return $this;
    }

    public function getIsProductNew(): ?bool
    {
        return $this->is_product_new;
    }

    public function setIsProductNew(bool $is_product_new): self
    {
        $this->is_product_new = $is_product_new;

        return $this;
    }

    public function getCategorie(): ?categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getInstruction(): ?string
    {
        return $this->instruction;
    }

    public function setInstruction(?string $instruction): self
    {
        $this->instruction = $instruction;

        return $this;
    }
}
