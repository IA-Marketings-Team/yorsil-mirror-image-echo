<?php

namespace App\Entity;

use App\Repository\FraiserviceboutiqueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FraiserviceboutiqueRepository::class)
 */
class Fraiserviceboutique
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
     * @ORM\ManyToOne(targetEntity=bout::class, inversedBy="fraiserviceboutiques")
     */
    private $boutique;

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

    public function getBoutique(): ?bout
    {
        return $this->boutique;
    }

    public function setBoutique(?bout $boutique): self
    {
        $this->boutique = $boutique;

        return $this;
    }
}
