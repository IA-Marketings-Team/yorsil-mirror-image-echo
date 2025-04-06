<?php

namespace App\Entity;

use App\Repository\PaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaysRepository::class)
 */
class Pays
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $prefixe;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $logo;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_api;

    /**
     * @ORM\OneToMany(targetEntity=Operateur::class, mappedBy="id_pays")
     */
    private $operateurs;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $type_api;

    /**
     * @ORM\OneToMany(targetEntity=TauxChange::class, mappedBy="pays_change")
     */
    private $tauxChanges;

    public function __construct()
    {
        $this->operateurs = new ArrayCollection();
        $this->tauxChanges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
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

    public function getPrefixe(): ?string
    {
        return $this->prefixe;
    }

    public function setPrefixe(string $prefixe): self
    {
        $this->prefixe = $prefixe;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getIsApi(): ?bool
    {
        return $this->is_api;
    }

    public function setIsApi(bool $is_api): self
    {
        $this->is_api = $is_api;

        return $this;
    }

    /**
     * @return Collection|Operateur[]
     */
    public function getOperateurs(): Collection
    {
        return $this->operateurs;
    }

    public function addOperateur(Operateur $operateur): self
    {
        if (!$this->operateurs->contains($operateur)) {
            $this->operateurs[] = $operateur;
            $operateur->setIdPays($this);
        }

        return $this;
    }

    public function removeOperateur(Operateur $operateur): self
    {
        if ($this->operateurs->removeElement($operateur)) {
            // set the owning side to null (unless already changed)
            if ($operateur->getIdPays() === $this) {
                $operateur->setIdPays(null);
            }
        }

        return $this;
    }

    public function getTypeApi(): ?string
    {
        return $this->type_api;
    }

    public function setTypeApi(?string $type_api): self
    {
        $this->type_api = $type_api;

        return $this;
    }

    /**
     * @return Collection|TauxChange[]
     */
    public function getTauxChanges(): Collection
    {
        return $this->tauxChanges;
    }

    public function addTauxChange(TauxChange $tauxChange): self
    {
        if (!$this->tauxChanges->contains($tauxChange)) {
            $this->tauxChanges[] = $tauxChange;
            $tauxChange->setPaysChange($this);
        }

        return $this;
    }

    public function removeTauxChange(TauxChange $tauxChange): self
    {
        if ($this->tauxChanges->removeElement($tauxChange)) {
            // set the owning side to null (unless already changed)
            if ($tauxChange->getPaysChange() === $this) {
                $tauxChange->setPaysChange(null);
            }
        }

        return $this;
    }
}
