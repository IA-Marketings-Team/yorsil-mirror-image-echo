<?php

namespace App\Entity;

use App\Repository\NotificationrechargementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationrechargementRepository::class)
 */
class Notificationrechargement
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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isRead;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity=credit::class, inversedBy="notificationrechargements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $credit;

    /**
     * @ORM\ManyToOne(targetEntity=bout::class, inversedBy="notificationrechargements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $boutique;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAdmin;

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

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getCredit(): ?credit
    {
        return $this->credit;
    }

    public function setCredit(?credit $credit): self
    {
        $this->credit = $credit;

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

    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(?bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }
}
