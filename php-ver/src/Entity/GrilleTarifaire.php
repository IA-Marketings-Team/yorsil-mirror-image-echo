<?php

namespace App\Entity;

use App\Repository\GrilleTarifaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GrilleTarifaireRepository::class)
 */
class GrilleTarifaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $famille;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $produit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $annulable;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $ref;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $gencode;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $tva;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prix_pdv;

    /**
     * @ORM\Column(type="float")
     */
    private $prix_yorsil;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $remise_pdv;

    /**
     * @ORM\Column(type="float")
     */
    private $prix_public_ttc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pourcentage_yorsil;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commission_distrib;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_ajout;

    /**
     * @ORM\OneToMany(targetEntity=GrilleTarifaireBoutique::class, mappedBy="grille_tarifaire")
     */
    private $grilleTarifaireBoutiques;

    public function __construct()
    {
        $this->grilleTarifaireBoutiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFamille(): ?string
    {
        return $this->famille;
    }

    public function setFamille(?string $famille): self
    {
        $this->famille = $famille;

        return $this;
    }

    public function getProduit(): ?string
    {
        return $this->produit;
    }

    public function setProduit(string $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function isAnnulable(): ?bool
    {
        return $this->annulable;
    }

    public function setAnnulable(bool $annulable): self
    {
        $this->annulable = $annulable;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): self
    {
        $this->ref = $ref;

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

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(?float $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getPrixPdv(): ?float
    {
        return $this->prix_pdv;
    }

    public function setPrixPdv(?float $prix_pdv): self
    {
        $this->prix_pdv = $prix_pdv;

        return $this;
    }

    public function getPrixYorsil(): ?float
    {
        return $this->prix_yorsil;
    }

    public function setPrixYorsil(float $prix_yorsil): self
    {
        $this->prix_yorsil = $prix_yorsil;

        return $this;
    }

    public function getRemisePdv(): ?float
    {
        return $this->remise_pdv;
    }

    public function setRemisePdv(?float $remise_pdv): self
    {
        $this->remise_pdv = $remise_pdv;

        return $this;
    }

    public function getPrixPublicTtc(): ?float
    {
        return $this->prix_public_ttc;
    }

    public function setPrixPublicTtc(float $prix_public_ttc): self
    {
        $this->prix_public_ttc = $prix_public_ttc;

        return $this;
    }

    public function getPourcentageYorsil(): ?float
    {
        return $this->pourcentage_yorsil;
    }

    public function setPourcentageYorsil(?float $pourcentage_yorsil): self
    {
        $this->pourcentage_yorsil = $pourcentage_yorsil;

        return $this;
    }

    public function getCommissionDistrib(): ?float
    {
        return $this->commission_distrib;
    }

    public function setCommissionDistrib(?float $commission_distrib): self
    {
        $this->commission_distrib = $commission_distrib;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): self
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }

    /**
     * @return Collection<int, GrilleTarifaireBoutique>
     */
    public function getGrilleTarifaireBoutiques(): Collection
    {
        return $this->grilleTarifaireBoutiques;
    }

    public function addGrilleTarifaireBoutique(GrilleTarifaireBoutique $grilleTarifaireBoutique): self
    {
        if (!$this->grilleTarifaireBoutiques->contains($grilleTarifaireBoutique)) {
            $this->grilleTarifaireBoutiques[] = $grilleTarifaireBoutique;
            $grilleTarifaireBoutique->setGrilleTarifaire($this);
        }

        return $this;
    }

    public function removeGrilleTarifaireBoutique(GrilleTarifaireBoutique $grilleTarifaireBoutique): self
    {
        if ($this->grilleTarifaireBoutiques->removeElement($grilleTarifaireBoutique)) {
            // set the owning side to null (unless already changed)
            if ($grilleTarifaireBoutique->getGrilleTarifaire() === $this) {
                $grilleTarifaireBoutique->setGrilleTarifaire(null);
            }
        }

        return $this;
    }

    public function getTauxCommission(): ?float
    {
        return $this->pourcentage_yorsil ? $this->pourcentage_yorsil / 100 : null;
    }
}
