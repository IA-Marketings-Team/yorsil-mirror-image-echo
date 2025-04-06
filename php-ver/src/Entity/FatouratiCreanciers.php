<?php

namespace App\Entity;

use App\Repository\FatouratiCreanciersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FatouratiCreanciersRepository::class)
 */
class FatouratiCreanciers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $code_creancier;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom_creancier;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_ajout;

    /**
     * @ORM\OneToMany(targetEntity=FatouratiCreances::class, mappedBy="creancier")
     */
    private $fatouratiCreances;

    /**
     * @ORM\OneToMany(targetEntity=FatouratiPaiement::class, mappedBy="creancier")
     */
    private $fatouratiPaiements;

    public function __construct()
    {
        $this->fatouratiCreances = new ArrayCollection();
        $this->fatouratiPaiements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeCreancier(): ?string
    {
        return $this->code_creancier;
    }

    public function setCodeCreancier(string $code_creancier): self
    {
        $this->code_creancier = $code_creancier;

        return $this;
    }

    public function getNomCreancier(): ?string
    {
        return $this->nom_creancier;
    }

    public function setNomCreancier(string $nom_creancier): self
    {
        $this->nom_creancier = $nom_creancier;

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
     * @return Collection|FatouratiCreances[]
     */
    public function getFatouratiCreances(): Collection
    {
        return $this->fatouratiCreances;
    }

    public function addFatouratiCreance(FatouratiCreances $fatouratiCreance): self
    {
        if (!$this->fatouratiCreances->contains($fatouratiCreance)) {
            $this->fatouratiCreances[] = $fatouratiCreance;
            $fatouratiCreance->setCreancier($this);
        }

        return $this;
    }

    public function removeFatouratiCreance(FatouratiCreances $fatouratiCreance): self
    {
        if ($this->fatouratiCreances->removeElement($fatouratiCreance)) {
            // set the owning side to null (unless already changed)
            if ($fatouratiCreance->getCreancier() === $this) {
                $fatouratiCreance->setCreancier(null);
            }
        }

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
            $fatouratiPaiement->setCreancier($this);
        }

        return $this;
    }

    public function removeFatouratiPaiement(FatouratiPaiement $fatouratiPaiement): self
    {
        if ($this->fatouratiPaiements->removeElement($fatouratiPaiement)) {
            // set the owning side to null (unless already changed)
            if ($fatouratiPaiement->getCreancier() === $this) {
                $fatouratiPaiement->setCreancier(null);
            }
        }

        return $this;
    }
}
