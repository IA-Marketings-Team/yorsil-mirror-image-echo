<?php

namespace App\Entity;

use App\Repository\SeuilpercepteurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeuilpercepteurRepository::class)
 */
class Seuilpercepteur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity=percept::class, inversedBy="seuilpercepteurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $percepteur;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="seuilpercepteurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admin;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPercepteur(): ?percept
    {
        return $this->percepteur;
    }

    public function setPercepteur(?percept $percepteur): self
    {
        $this->percepteur = $percepteur;

        return $this;
    }

    public function getAdmin(): ?user
    {
        return $this->admin;
    }

    public function setAdmin(?user $admin): self
    {
        $this->admin = $admin;

        return $this;
    }
}
