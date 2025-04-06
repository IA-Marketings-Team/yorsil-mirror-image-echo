<?php

namespace App\Entity;

use App\Repository\TypeOffresRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeOffresRepository::class)
 */
class TypeOffres
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
     * @ORM\ManyToOne(targetEntity=Operateur::class, inversedBy="typeOffres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $operateur;

    /**
     * @ORM\OneToMany(targetEntity=Offres::class, mappedBy="type_offres")
     */
    private $offres;

    public function __construct()
    {
        $this->offres = new ArrayCollection();
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

    public function getOperateur(): ?operateur
    {
        return $this->operateur;
    }

    public function setOperateur(?operateur $operateur): self
    {
        $this->operateur = $operateur;

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
            $offre->setTypeOffres($this);
        }

        return $this;
    }

    public function removeOffre(Offres $offre): self
    {
        if ($this->offres->removeElement($offre)) {
            // set the owning side to null (unless already changed)
            if ($offre->getTypeOffres() === $this) {
                $offre->setTypeOffres(null);
            }
        }

        return $this;
    }
}
