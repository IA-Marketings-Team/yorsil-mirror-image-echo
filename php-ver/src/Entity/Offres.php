<?php

namespace App\Entity;

use App\Repository\OffresRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffresRepository::class)
 */
class Offres
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
    private $montant;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montant_devise;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Operateur::class, inversedBy="offres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $operateur;

    /**
     * @ORM\ManyToOne(targetEntity=TypeOffres::class, inversedBy="offres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type_offres;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $devise;

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

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMontantDevise(): ?float
    {
        return $this->montant_devise;
    }

    public function setMontantDevise(?float $montant_devise): self
    {
        $this->montant_devise = $montant_devise;

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

    public function getOperateur(): ?operateur
    {
        return $this->operateur;
    }

    public function setOperateur(?operateur $operateur): self
    {
        $this->operateur = $operateur;

        return $this;
    }

    public function getTypeOffres(): ?TypeOffres
    {
        return $this->type_offres;
    }

    public function setTypeOffres(?TypeOffres $type_offres): self
    {
        $this->type_offres = $type_offres;

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
}
