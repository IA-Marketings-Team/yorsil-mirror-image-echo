<?php

namespace App\Entity;

use App\Repository\FraiserviceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FraiserviceRepository::class)
 */
class Fraiservice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $type;

    /**
     * @ORM\Column(type="float")
     */
    private $pourcentage;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="float")
     */
    private $pourcentage_boutique;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPourcentage(): ?float
    {
        return $this->pourcentage;
    }

    public function setPourcentage(float $pourcentage): self
    {
        $this->pourcentage = $pourcentage;

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

    public function getPourcentageBoutique(): ?float
    {
        return $this->pourcentage_boutique;
    }

    public function setPourcentageBoutique(float $pourcentage_boutique): self
    {
        $this->pourcentage_boutique = $pourcentage_boutique;

        return $this;
    }
}
