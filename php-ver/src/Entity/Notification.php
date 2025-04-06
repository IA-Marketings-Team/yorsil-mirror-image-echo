<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotificationRepository::class)
 */
class Notification
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
     * @ORM\ManyToOne(targetEntity=Transfert::class, inversedBy="notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $transfert;

    /**
     * @ORM\ManyToOne(targetEntity=Bout::class, inversedBy="notifications")
     */
    private $boutique;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_admin;

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

    public function setIsRead(?bool $isRead): self
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

    public function getTransfert(): ?transfert
    {
        return $this->transfert;
    }

    public function setTransfert(?transfert $transfert): self
    {
        $this->transfert = $transfert;

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
        return $this->is_admin;
    }

    public function setIsAdmin(bool $is_admin): self
    {
        $this->is_admin = $is_admin;

        return $this;
    }
}
