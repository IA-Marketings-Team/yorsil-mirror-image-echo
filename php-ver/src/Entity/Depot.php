<?php

namespace App\Entity;

use App\Repository\DepotRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Percept::class, inversedBy="depots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $percepteur;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isvalid;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isarchive;

    /**
     * @ORM\ManyToOne(targetEntity=Fichier::class, inversedBy="depots")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPercepteur(): ?percept
    {
        return $this->percepteur;
    }

    public function setPercepteur(?percept $percepteur): self
    {
        $this->percepteur = $percepteur;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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

    public function getIsvalid(): ?bool
    {
        return $this->isvalid;
    }

    public function setIsvalid(?bool $isvalid): self
    {
        $this->isvalid = $isvalid;

        return $this;
    }

    public function getIsarchive(): ?bool
    {
        return $this->isarchive;
    }

    public function setIsarchive(?bool $isarchive): self
    {
        $this->isarchive = $isarchive;

        return $this;
    }

    public function getFile(): ?fichier
    {
        return $this->file;
    }

    public function setFile(?fichier $file): self
    {
        $this->file = $file;

        return $this;
    }
}
