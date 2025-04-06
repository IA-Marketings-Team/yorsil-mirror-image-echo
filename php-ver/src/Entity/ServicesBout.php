<?php

namespace App\Entity;

use App\Repository\ServicesBoutRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServicesBoutRepository::class)
 */
class ServicesBout
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $service = [];

    /**
     * @ORM\ManyToOne(targetEntity=Bout::class, inversedBy="servicesBouts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $boutique;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?array
    {
        return $this->service;
    }

    public function setService(array $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getBoutique(): ?Bout
    {
        return $this->boutique;
    }

    public function setBoutique(?Bout $boutique): self
    {
        $this->boutique = $boutique;

        return $this;
    }
}
