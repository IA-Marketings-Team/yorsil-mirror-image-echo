<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 */
class Account
{
    /**
     * @ORM\Id
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $solde;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $owner;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $post_payed_amount;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_hybrid;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getOwner(): ?int
    {
        return $this->owner;
    }

    public function setOwner(?int $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPostPayedAmount(): ?float
    {
        return $this->post_payed_amount;
    }

    public function setPostPayedAmount(?float $post_payed_amount): self
    {
        $this->post_payed_amount = $post_payed_amount;

        return $this;
    }

    public function getIsHybrid(): ?bool
    {
        return $this->is_hybrid;
    }

    public function setIsHybrid(?bool $is_hybrid): self
    {
        $this->is_hybrid = $is_hybrid;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
