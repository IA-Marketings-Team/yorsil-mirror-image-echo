<?php

namespace App\Entity;

use App\Repository\DingproductdescriptionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DingproductdescriptionsRepository::class)
 */
class Dingproductdescriptions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $skucode;

    /**
     * @ORM\Column(type="json")
     */
    private $value = [];

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function setValue(array $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value, 
            'skucode' => $this->skucode,
        ];
    }
}
