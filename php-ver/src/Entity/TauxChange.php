<?php

namespace App\Entity;

use App\Repository\TauxChangeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TauxChangeRepository::class)
 */
class TauxChange
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_change;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_change;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="tauxChanges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pays_change;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $devise;

    /**
     * @ORM\OneToMany(targetEntity=FatouratiPaiement::class, mappedBy="taux_change")
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

    public function getMontantChange(): ?float
    {
        return $this->montant_change;
    }

    public function setMontantChange(float $montant_change): self
    {
        $this->montant_change = $montant_change;

        return $this;
    }

    public function getDateChange(): ?\DateTimeInterface
    {
        return $this->date_change;
    }

    public function setDateChange(\DateTimeInterface $date_change): self
    {
        $this->date_change = $date_change;

        return $this;
    }

    public function getPaysChange(): ?Pays
    {
        return $this->pays_change;
    }

    public function setPaysChange(?Pays $pays_change): self
    {
        $this->pays_change = $pays_change;

        return $this;
    }

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): self
    {
        $this->devise = $devise;

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
            $fatouratiPaiement->setTauxChange($this);
        }

        return $this;
    }

    public function removeFatouratiPaiement(FatouratiPaiement $fatouratiPaiement): self
    {
        if ($this->fatouratiPaiements->removeElement($fatouratiPaiement)) {
            // set the owning side to null (unless already changed)
            if ($fatouratiPaiement->getTauxChange() === $this) {
                $fatouratiPaiement->setTauxChange(null);
            }
        }

        return $this;
    }
}
