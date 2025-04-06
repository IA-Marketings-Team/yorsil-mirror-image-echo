<?php

namespace App\Entity;

use App\Repository\RechargeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RechargeRepository::class)
 */
class Recharge
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
    private $articles = [];

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $saleRef;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $internalRef;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $processState;

    /**
     * @ORM\Column(type="datetime")
     */
    private $saleDate;

    /**
     * @ORM\Column(type="json")
     */
    private $productInformations = [];

    /**
     * @ORM\Column(type="integer")
     */
    private $qty;

    /**
     * @ORM\Column(type="json")
     */
    private $voucher = [];

    /**
     * @ORM\ManyToOne(targetEntity=bout::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $boutique;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $tva;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $frais;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $frais_boutique;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticles(): ?array
    {
        return $this->articles;
    }

    public function setArticles(array $articles): self
    {
        $this->articles = $articles;

        return $this;
    }

    public function getSaleRef(): ?string
    {
        return $this->saleRef;
    }

    public function setSaleRef(string $saleRef): self
    {
        $this->saleRef = $saleRef;

        return $this;
    }

    public function getInternalRef(): ?string
    {
        return $this->internalRef;
    }

    public function setInternalRef(string $internalRef): self
    {
        $this->internalRef = $internalRef;

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

    public function getProcessState(): ?string
    {
        return $this->processState;
    }

    public function setProcessState(string $processState): self
    {
        $this->processState = $processState;

        return $this;
    }

    public function getSaleDate(): ?\DateTimeInterface
    {
        return $this->saleDate;
    }

    public function setSaleDate(\DateTimeInterface $saleDate): self
    {
        $this->saleDate = $saleDate;

        return $this;
    }

    public function getProductInformations(): ?array
    {
        return $this->productInformations;
    }

    public function setProductInformations(array $productInformations): self
    {
        $this->productInformations = $productInformations;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getVoucher(): ?array
    {
        return $this->voucher;
    }

    public function setVoucher(array $voucher): self
    {
        $this->voucher = $voucher;

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

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(?float $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getFrais(): ?float
    {
        return $this->frais;
    }

    public function setFrais(?float $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getFraisBoutique(): ?float
    {
        return $this->frais_boutique;
    }

    public function setFraisBoutique(?float $frais_boutique): self
    {
        $this->frais_boutique = $frais_boutique;

        return $this;
    }
}
