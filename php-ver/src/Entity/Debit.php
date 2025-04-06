<?php

namespace App\Entity;

use App\Repository\DebitRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DebitRepository::class)
 */
class Debit
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity=Bout::class, inversedBy="debits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bout;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="debits")
     * @ORM\JoinColumn(nullable=true)
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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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
