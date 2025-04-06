<?php

namespace App\Entity;

use App\Repository\RechargeflexiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RechargeflexiRepository::class)
 */
class Rechargeflexi
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
    private $numero;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nomoffre;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $isvalid;

    /**
     * @ORM\ManyToOne(targetEntity=Operateur::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $operateur;

    /**
     * @ORM\ManyToOne(targetEntity=user::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $frais_bout;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $frais;

    /**
     * @ORM\OneToMany(targetEntity=NotificationTrx::class, mappedBy="trx")
     */
    private $notificationTrxes;

    public function __construct()
    {
        $this->notificationTrxes = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNomoffre(): ?string
    {
        return $this->nomoffre;
    }

    public function setNomoffre(string $nomoffre): self
    {
        $this->nomoffre = $nomoffre;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getIsvalid(): ?bool
    {
        return $this->isvalid;
    }

    public function setIsvalid(bool $isvalid): self
    {
        $this->isvalid = $isvalid;

        return $this;
    }

    public function getOperateur(): ?operateur
    {
        return $this->operateur;
    }

    public function setOperateur(?operateur $operateur): self
    {
        $this->operateur = $operateur;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFraisBout(): ?float
    {
        return $this->frais_bout;
    }

    public function setFraisBout(?float $frais_bout): self
    {
        $this->frais_bout = $frais_bout;

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

    /**
     * @return Collection|NotificationTrx[]
     */
    public function getNotificationTrxes(): Collection
    {
        return $this->notificationTrxes;
    }

    public function addNotificationTrx(NotificationTrx $notificationTrx): self
    {
        if (!$this->notificationTrxes->contains($notificationTrx)) {
            $this->notificationTrxes[] = $notificationTrx;
            $notificationTrx->setTrx($this);
        }

        return $this;
    }

    public function removeNotificationTrx(NotificationTrx $notificationTrx): self
    {
        if ($this->notificationTrxes->removeElement($notificationTrx)) {
            // set the owning side to null (unless already changed)
            if ($notificationTrx->getTrx() === $this) {
                $notificationTrx->setTrx(null);
            }
        }

        return $this;
    }
}
