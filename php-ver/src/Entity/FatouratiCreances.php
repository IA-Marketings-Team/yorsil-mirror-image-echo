<?php

namespace App\Entity;

use App\Repository\FatouratiCreancesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FatouratiCreancesRepository::class)
 */
class FatouratiCreances
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $code_creance;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom_creance;

    /**
     * @ORM\ManyToOne(targetEntity=FatouratiCreanciers::class, inversedBy="fatouratiCreances")
     */
    private $creancier;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_ajout;

    /**
     * @ORM\OneToMany(targetEntity=FatouratiPaiement::class, mappedBy="creance")
     */
    private $fatouratiPaiements;

    public function __construct()
    {
        $this->fatouratiPaiements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeCreance(): ?string
    {
        return $this->code_creance;
    }

    public function setCodeCreance(string $code_creance): self
    {
        $this->code_creance = $code_creance;

        return $this;
    }

    public function getNomCreance(): ?string
    {
        return $this->nom_creance;
    }

    public function setNomCreance(string $nom_creance): self
    {
        $this->nom_creance = $nom_creance;

        return $this;
    }

    public function getCreancier(): ?FatouratiCreanciers
    {
        return $this->creancier;
    }

    public function setCreancier(?FatouratiCreanciers $creancier): self
    {
        $this->creancier = $creancier;

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
     * @return Collection|FatouratiPaiement[]
     */
    public function getFatouratiPaiements(): Collection
    {
        return $this->fatouratiPaiements;
    }

    public function addFatouratiPaiement(FatouratiPaiement $fatouratiPaiement): self
    {
        if (!$this->fatouratiPaiements->contains($fatouratiPaiement)) {
            $this->fatouratiPaiements[] = $fatouratiPaiement;
            $fatouratiPaiement->setCreance($this);
        }

        return $this;
    }

    public function removeFatouratiPaiement(FatouratiPaiement $fatouratiPaiement): self
    {
        if ($this->fatouratiPaiements->removeElement($fatouratiPaiement)) {
            // set the owning side to null (unless already changed)
            if ($fatouratiPaiement->getCreance() === $this) {
                $fatouratiPaiement->setCreance(null);
            }
        }

        return $this;
    }
}
