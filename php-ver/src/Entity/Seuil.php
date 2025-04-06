<?php

namespace App\Entity;

use App\Repository\SeuilRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeuilRepository::class)
 */
class Seuil
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
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Bout::class, inversedBy="seuils")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bout;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="seuils")
     * @ORM\JoinColumn(nullable=false)
     */
    private $admin;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getBout(): ?Bout
    {
        return $this->bout;
    }

    public function setBout(?Bout $bout): self
    {
        $this->bout = $bout;

        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

        return $this;
    }
}
