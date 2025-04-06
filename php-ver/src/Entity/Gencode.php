<?php

namespace App\Entity;

use App\Repository\GencodeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GencodeRepository::class)
 */
class Gencode
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $operateur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $valeur;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOperateur(): ?string
    {
        return $this->operateur;
    }

    public function setOperateur(string $operateur): self
    {
        $this->operateur = $operateur;

        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
