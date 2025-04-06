<?php

namespace App\Entity;

use App\Repository\SeuilBilleterieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SeuilBilleterieRepository::class)
 */
class SeuilBilleterie
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
     * @ORM\ManyToOne(targetEntity=Bout::class, inversedBy="seuilBilleteries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bout;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="seuilBilleteries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $users;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $valeur;

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

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): self
    {
        $this->users = $users;

        return $this;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(?float $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }
}
