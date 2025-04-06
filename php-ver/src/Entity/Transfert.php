<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransfertRepository::class)
 */
class Transfert
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Bout::class, inversedBy="transferts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bout;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $codePays;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $operateur;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $trxId;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateUpdate;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $devise_local;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $devise_compte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $montant_devise;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $operateur_info = [];

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $fx = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="transfert")
     */
    private $notifications;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $commission;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $api_name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $frais_boutique;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBout(): ?Bout
    {
        return $this->bout;
    }

    public function setBout(?Bout $bout): self
    {
        $this->bout = $bout;

        return $this;
    }

    public function getCodePays(): ?string
    {
        return $this->codePays;
    }

    public function setCodePays(string $codePays): self
    {
        $this->codePays = $codePays;

        return $this;
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

    public function getOperateur(): ?string
    {
        return $this->operateur;
    }

    public function setOperateur(string $operateur): self
    {
        $this->operateur = $operateur;

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

    public function getTrxId(): ?int
    {
        return $this->trxId;
    }

    public function setTrxId(int $trxId): self
    {
        $this->trxId = $trxId;

        return $this;
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

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(?\DateTimeInterface $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(?string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getDeviseLocal(): ?string
    {
        return $this->devise_local;
    }

    public function setDeviseLocal(?string $devise_local): self
    {
        $this->devise_local = $devise_local;

        return $this;
    }

    public function getDeviseCompte(): ?string
    {
        return $this->devise_compte;
    }

    public function setDeviseCompte(?string $devise_compte): self
    {
        $this->devise_compte = $devise_compte;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMontantDevise(): ?string
    {
        return $this->montant_devise;
    }

    public function setMontantDevise(?string $montant_devise): self
    {
        $this->montant_devise = $montant_devise;

        return $this;
    }

    public function getOperateurInfo(): ?array
    {
        return $this->operateur_info;
    }

    public function setOperateurInfo(?array $operateur_info): self
    {
        $this->operateur_info = $operateur_info;

        return $this;
    }

    public function getFx(): ?array
    {
        return $this->fx;
    }

    public function setFx(?array $fx): self
    {
        $this->fx = $fx;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setTransfert($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getTransfert() === $this) {
                $notification->setTransfert(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCommission(): ?float
    {
        return $this->commission;
    }

    public function setCommission(float $commission): self
    {
        $this->commission = $commission;

        return $this;
    }

    public function getApiName(): ?string
    {
        return $this->api_name;
    }

    public function setApiName(?string $api_name): self
    {
        $this->api_name = $api_name;

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
