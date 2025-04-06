<?php

namespace App\Entity;

use App\Repository\OperateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OperateurRepository::class)
 */
class Operateur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nompays;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $actif;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="operateurs")
     */
    private $id_pays;

    /**
     * @ORM\OneToMany(targetEntity=Offres::class, mappedBy="operateur")
     */
    private $offres;

    /**
     * @ORM\OneToMany(targetEntity=TypeOffres::class, mappedBy="operateur")
     */
    private $typeOffres;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $longueur_code;

    /**
     * @ORM\ManyToOne(targetEntity=Fichier::class, inversedBy="operateurs")
     */
    private $logo;

    /**
     * @ORM\OneToMany(targetEntity=ProduitPhysique::class, mappedBy="operateur")
     */
    private $produitPhysiques;

    public function __construct()
    {
        $this->offres = new ArrayCollection();
        $this->typeOffres = new ArrayCollection();
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

    public function getNomPays(): ?string
    {
        return $this->nompays;
    }

    public function setNomPays(string $nompays): self
    {
        $this->nompays = $nompays;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getIdPays(): ?pays
    {
        return $this->id_pays;
    }

    public function setIdPays(?pays $id_pays): self
    {
        $this->id_pays = $id_pays;

        return $this;
    }

    /**
     * @return Collection|Offres[]
     */
    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offres $offre): self
    {
        if (!$this->offres->contains($offre)) {
            $this->offres[] = $offre;
            $offre->setOperateur($this);
        }

        return $this;
    }

    public function removeOffre(Offres $offre): self
    {
        if ($this->offres->removeElement($offre)) {
            // set the owning side to null (unless already changed)
            if ($offre->getOperateur() === $this) {
                $offre->setOperateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TypeOffres[]
     */
    public function getTypeOffres(): Collection
    {
        return $this->typeOffres;
    }

    public function addTypeOffre(TypeOffres $typeOffre): self
    {
        if (!$this->typeOffres->contains($typeOffre)) {
            $this->typeOffres[] = $typeOffre;
            $typeOffre->setOperateur($this);
        }

        return $this;
    }

    public function removeTypeOffre(TypeOffres $typeOffre): self
    {
        if ($this->typeOffres->removeElement($typeOffre)) {
            // set the owning side to null (unless already changed)
            if ($typeOffre->getOperateur() === $this) {
                $typeOffre->setOperateur(null);
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

    public function getLongueurCode(): ?string
    {
        return $this->longueur_code;
    }

    public function setLongueurCode(?string $longueur_code): self
    {
        $this->longueur_code = $longueur_code;

        return $this;
    }

    public function getLogo(): ?fichier
    {
        return $this->logo;
    }

    public function setLogo(?fichier $logo): self
    {
        $this->logo = $logo;

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
            $produitPhysique->setOperateur($this);
        }

        return $this;
    }

    public function removeProduitPhysique(ProduitPhysique $produitPhysique): self
    {
        if ($this->produitPhysiques->removeElement($produitPhysique)) {
            // set the owning side to null (unless already changed)
            if ($produitPhysique->getOperateur() === $this) {
                $produitPhysique->setOperateur(null);
            }
        }

        return $this;
    }
}
