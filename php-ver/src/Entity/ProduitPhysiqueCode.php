<?php

namespace App\Entity;

use App\Repository\ProduitPhysiqueCodeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitPhysiqueCodeRepository::class)
 */
class ProduitPhysiqueCode
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity=ProduitPhysique::class, inversedBy="produitPhysiqueCodes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $produit_physique;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    public function getProduitPhysique(): ?ProduitPhysique
    {
        return $this->produit_physique;
    }

    public function setProduitPhysique(?ProduitPhysique $produit_physique): self
    {
        $this->produit_physique = $produit_physique;

        return $this;
    }
}
