<?php

namespace App\Entity;

use App\Repository\NotificationTrxRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationTrxRepository::class)
 */
class NotificationTrx
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
     * @ORM\Column(type="boolean")
     */
    private $is_read;

    /**
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_admin;

    /**
     * @ORM\ManyToOne(targetEntity=Bout::class, inversedBy="notificationTrxes")
     */
    private $boutique;

    /**
     * @ORM\ManyToOne(targetEntity=Rechargeflexi::class, inversedBy="notificationTrxes")
     */
    private $trx;

    public function __construct()
    {
        $this->is_read = false;
        $this->date    = new \DateTime();
    }

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
        return $this->is_read;
    }

    public function setIsRead(bool $is_read): self
    {
        $this->is_read = $is_read;

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

    public function getIsAdmin(): ?bool
    {
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): self
    {
        $this->is_admin = $is_admin;

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

    public function getTrx(): ?Rechargeflexi
    {
        return $this->trx;
    }

    public function setTrx(?Rechargeflexi $trx): self
    {
        $this->trx = $trx;

        return $this;
    }
}
