<?php

namespace App\Entity;

use App\Repository\DingproductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DingproductRepository::class)
 */
class Dingproduct
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
    private $transfertType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $redemptionmechanism;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $benefits;

    /**
     * @ORM\Column(type="json")
     */
    private $value = [];

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $providercode;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $skucode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransfertType(): ?string
    {
        return $this->transfertType;
    }

    public function setTransfertType(string $transfertType): self
    {
        $this->transfertType = $transfertType;

        return $this;
    }

    public function getRedemptionmechanism(): ?string
    {
        return $this->redemptionmechanism;
    }

    public function setRedemptionmechanism(string $redemptionmechanism): self
    {
        $this->redemptionmechanism = $redemptionmechanism;

        return $this;
    }

    public function getBenefits(): ?string
    {
        return $this->benefits;
    }

    public function setBenefits(string $benefits): self
    {
        $this->benefits = $benefits;

        return $this;
    }

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function setValue(array $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getProvidercode(): ?string
    {
        return $this->providercode;
    }

    public function setProvidercode(string $providercode): self
    {
        $this->providercode = $providercode;

        return $this;
    }

    public function getSkucode(): ?string
    {
        return $this->skucode;
    }

    public function setSkucode(string $skucode): self
    {
        $this->skucode = $skucode;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'transfertType' => $this->transfertType,
            'redemptionmechanism' => $this->redemptionmechanism,
            'benefits' => $this->benefits,
            'value' => $this->value, // Assurez-vous que `value` est un tableau
            'providercode' => $this->providercode,
            'skucode' => $this->skucode,
        ];
    }
}
