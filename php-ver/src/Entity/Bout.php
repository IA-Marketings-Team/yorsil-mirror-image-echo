<?php

namespace App\Entity;

use App\Repository\BoutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BoutRepository::class)
 */
class Bout
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $nRcs;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $numMobile;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $codePost;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Percept::class, inversedBy="bouts")
     */
    private $percept;

    /**
     * @ORM\OneToMany(targetEntity=Credit::class, mappedBy="bout", orphanRemoval=true)
     */
    private $credits;

    /**
     * @ORM\OneToMany(targetEntity=Debit::class, mappedBy="bout", orphanRemoval=true)
     */
    private $debits;

    /**
     * @ORM\OneToMany(targetEntity=Gestecommercial::class, mappedBy="bout", orphanRemoval=true)
     */
    private $gestecommercials;

    /**
     * @ORM\OneToMany(targetEntity=Seuil::class, mappedBy="bout", orphanRemoval=true)
     */
    private $seuils;

    /**
     * @ORM\OneToMany(targetEntity=Transfert::class, mappedBy="bout", orphanRemoval=true)
     */
    private $transferts;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $preuveCabis;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $cinGerant;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="boutique")
     */
    private $notifications;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_active;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_cgv;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_creation;

    /**
     * @ORM\OneToMany(targetEntity=SeuilBilleterie::class, mappedBy="bout", orphanRemoval=true)
     */
    private $seuilBilleteries;

    /**
     * @ORM\OneToMany(targetEntity=Notificationrechargement::class, mappedBy="boutique")
     */
    private $notificationrechargements;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $facturation;

    /**
     * @ORM\OneToMany(targetEntity=Fraiserviceboutique::class, mappedBy="boutique")
     */
    private $fraiserviceboutiques;

    /**
     * @ORM\OneToMany(targetEntity=NotificationTrx::class, mappedBy="boutique")
     */
    private $notificationTrxes;

    /**
     * @ORM\OneToMany(targetEntity=FatouratiPaiement::class, mappedBy="boutique")
     */
    private $fatouratiPaiements;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $siren;

    /**
     * @ORM\OneToMany(targetEntity=GrilleTarifaireBoutique::class, mappedBy="boutique")
     */
    private $grilleTarifaireBoutiques;

    /**
     * @ORM\OneToMany(targetEntity=ServicesBout::class, mappedBy="boutique", orphanRemoval=true)
     */
    private $servicesBouts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pieceIdentity;

    public function __construct()
    {
        $this->credits = new ArrayCollection();
        $this->debits = new ArrayCollection();
        $this->gestecommercials = new ArrayCollection();
        $this->seuils = new ArrayCollection();
        $this->transferts = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->seuilBilleteries = new ArrayCollection();
        $this->notificationrechargements = new ArrayCollection();
        $this->fraiserviceboutiques = new ArrayCollection();
        $this->notificationTrxes = new ArrayCollection();
        $this->fatouratiPaiements = new ArrayCollection();
        $this->grilleTarifaireBoutiques = new ArrayCollection();
        $this->servicesBouts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNRcs(): ?string
    {
        return $this->nRcs;
    }

    public function setNRcs(?string $nRcs): self
    {
        $this->nRcs = $nRcs;

        return $this;
    }

    public function getNumMobile(): ?string
    {
        return $this->numMobile;
    }

    public function setNumMobile(?string $numMobile): self
    {
        $this->numMobile = $numMobile;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePost(): ?string
    {
        return $this->codePost;
    }

    public function setCodePost(?string $codePost): self
    {
        $this->codePost = $codePost;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPercept(): ?Percept
    {
        return $this->percept;
    }

    public function setPercept(?Percept $percept): self
    {
        $this->percept = $percept;

        return $this;
    }

    /**
     * @return Collection|Credit[]
     */
    public function getCredits(): Collection
    {
        return $this->credits;
    }

    public function addCredit(Credit $credit): self
    {
        if (!$this->credits->contains($credit)) {
            $this->credits[] = $credit;
            $credit->setBout($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): self
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getBout() === $this) {
                $credit->setBout(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Debit[]
     */
    public function getDebits(): Collection
    {
        return $this->debits;
    }

    public function addDebit(Debit $debit): self
    {
        if (!$this->debits->contains($debit)) {
            $this->debits[] = $debit;
            $debit->setBout($this);
        }

        return $this;
    }

    public function removeDebit(Debit $debit): self
    {
        if ($this->debits->removeElement($debit)) {
            // set the owning side to null (unless already changed)
            if ($debit->getBout() === $this) {
                $debit->setBout(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Gestecommercial[]
     */
    public function getGestecommercials(): Collection
    {
        return $this->gestecommercials;
    }

    public function addGestecommercial(Gestecommercial $gestecommercial): self
    {
        if (!$this->gestecommercials->contains($gestecommercial)) {
            $this->gestecommercials[] = $gestecommercial;
            $gestecommercial->setBout($this);
        }

        return $this;
    }

    public function removeGestecommercial(Gestecommercial $gestecommercial): self
    {
        if ($this->gestecommercials->removeElement($gestecommercial)) {
            // set the owning side to null (unless already changed)
            if ($gestecommercial->getBout() === $this) {
                $gestecommercial->setBout(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Seuil[]
     */
    public function getSeuils(): Collection
    {
        return $this->seuils;
    }

    public function addSeuil(Seuil $seuil): self
    {
        if (!$this->seuils->contains($seuil)) {
            $this->seuils[] = $seuil;
            $seuil->setBout($this);
        }

        return $this;
    }

    public function removeSeuil(Seuil $seuil): self
    {
        if ($this->seuils->removeElement($seuil)) {
            // set the owning side to null (unless already changed)
            if ($seuil->getBout() === $this) {
                $seuil->setBout(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transfert[]
     */
    public function getTransferts(): Collection
    {
        return $this->transferts;
    }

    public function addTransfert(Transfert $transfert): self
    {
        if (!$this->transferts->contains($transfert)) {
            $this->transferts[] = $transfert;
            $transfert->setBout($this);
        }

        return $this;
    }

    public function removeTransfert(Transfert $transfert): self
    {
        if ($this->transferts->removeElement($transfert)) {
            // set the owning side to null (unless already changed)
            if ($transfert->getBout() === $this) {
                $transfert->setBout(null);
            }
        }

        return $this;
    }

    public function getPreuveCabis(): ?string
    {
        return $this->preuveCabis;
    }

    public function setPreuveCabis(?string $preuveCabis): self
    {
        $this->preuveCabis = $preuveCabis;

        return $this;
    }

    public function getCinGerant(): ?string
    {
        return $this->cinGerant;
    }

    public function setCinGerant(?string $cinGerant): self
    {
        $this->cinGerant = $cinGerant;

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
            $notification->setBoutique($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getBoutique() === $this) {
                $notification->setBoutique(null);
            }
        }

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getIsCgv(): ?bool
    {
        return $this->is_cgv;
    }

    public function setIsCgv(bool $is_cgv): self
    {
        $this->is_cgv = $is_cgv;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    /**
     * @return Collection|SeuilBilleterie[]
     */
    public function getSeuilBilleteries(): Collection
    {
        return $this->seuilBilleteries;
    }

    public function addSeuilBilletery(SeuilBilleterie $seuilBilletery): self
    {
        if (!$this->seuilBilleteries->contains($seuilBilletery)) {
            $this->seuilBilleteries[] = $seuilBilletery;
            $seuilBilletery->setBout($this);
        }

        return $this;
    }

    public function removeSeuilBilletery(SeuilBilleterie $seuilBilletery): self
    {
        if ($this->seuilBilleteries->removeElement($seuilBilletery)) {
            // set the owning side to null (unless already changed)
            if ($seuilBilletery->getBout() === $this) {
                $seuilBilletery->setBout(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notificationrechargement[]
     */
    public function getNotificationrechargements(): Collection
    {
        return $this->notificationrechargements;
    }

    public function addNotificationrechargement(Notificationrechargement $notificationrechargement): self
    {
        if (!$this->notificationrechargements->contains($notificationrechargement)) {
            $this->notificationrechargements[] = $notificationrechargement;
            $notificationrechargement->setBoutique($this);
        }

        return $this;
    }

    public function removeNotificationrechargement(Notificationrechargement $notificationrechargement): self
    {
        if ($this->notificationrechargements->removeElement($notificationrechargement)) {
            // set the owning side to null (unless already changed)
            if ($notificationrechargement->getBoutique() === $this) {
                $notificationrechargement->setBoutique(null);
            }
        }

        return $this;
    }

    public function getFacturation(): ?string
    {
        return $this->facturation;
    }

    public function setFacturation(string $facturation): self
    {
        $this->facturation = $facturation;

        return $this;
    }

    /**
     * @return Collection|Fraiserviceboutique[]
     */
    public function getFraiserviceboutiques(): Collection
    {
        return $this->fraiserviceboutiques;
    }

    public function addFraiserviceboutique(Fraiserviceboutique $fraiserviceboutique): self
    {
        if (!$this->fraiserviceboutiques->contains($fraiserviceboutique)) {
            $this->fraiserviceboutiques[] = $fraiserviceboutique;
            $fraiserviceboutique->setBoutique($this);
        }

        return $this;
    }

    public function removeFraiserviceboutique(Fraiserviceboutique $fraiserviceboutique): self
    {
        if ($this->fraiserviceboutiques->removeElement($fraiserviceboutique)) {
            // set the owning side to null (unless already changed)
            if ($fraiserviceboutique->getBoutique() === $this) {
                $fraiserviceboutique->setBoutique(null);
            }
        }

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
            $notificationTrx->setBoutique($this);
        }

        return $this;
    }

    public function removeNotificationTrx(NotificationTrx $notificationTrx): self
    {
        if ($this->notificationTrxes->removeElement($notificationTrx)) {
            // set the owning side to null (unless already changed)
            if ($notificationTrx->getBoutique() === $this) {
                $notificationTrx->setBoutique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FatouratiPaiement[]
     */
    public function getFatouratiPaiements(): Collection
    {
        return $this->fatouratiPaiements;
    }

    public function addFatouratiPaiement(FatouratiPaiement $fatouratiPaiement): self
    {
        if (!$this->fatouratiPaiements->contains($fatouratiPaiement)) {
            $this->fatouratiPaiements[] = $fatouratiPaiement;
            $fatouratiPaiement->setBoutique($this);
        }

        return $this;
    }

    public function removeFatouratiPaiement(FatouratiPaiement $fatouratiPaiement): self
    {
        if ($this->fatouratiPaiements->removeElement($fatouratiPaiement)) {
            // set the owning side to null (unless already changed)
            if ($fatouratiPaiement->getBoutique() === $this) {
                $fatouratiPaiement->setBoutique(null);
            }
        }

        return $this;
    }

    public function getSiren(): ?string
    {
        return $this->siren;
    }

    public function setSiren(?string $siren): self
    {
        $this->siren = $siren;

        return $this;
    }

    /**
     * @return Collection<int, GrilleTarifaireBoutique>
     */
    public function getGrilleTarifaireBoutiques(): Collection
    {
        return $this->grilleTarifaireBoutiques;
    }

    public function addGrilleTarifaireBoutique(GrilleTarifaireBoutique $grilleTarifaireBoutique): self
    {
        if (!$this->grilleTarifaireBoutiques->contains($grilleTarifaireBoutique)) {
            $this->grilleTarifaireBoutiques[] = $grilleTarifaireBoutique;
            $grilleTarifaireBoutique->setBoutique($this);
        }

        return $this;
    }

    public function removeGrilleTarifaireBoutique(GrilleTarifaireBoutique $grilleTarifaireBoutique): self
    {
        if ($this->grilleTarifaireBoutiques->removeElement($grilleTarifaireBoutique)) {
            // set the owning side to null (unless already changed)
            if ($grilleTarifaireBoutique->getBoutique() === $this) {
                $grilleTarifaireBoutique->setBoutique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ServicesBout>
     */
    public function getServicesBouts(): Collection
    {
        return $this->servicesBouts;
    }

    public function addServicesBout(ServicesBout $servicesBout): self
    {
        if (!$this->servicesBouts->contains($servicesBout)) {
            $this->servicesBouts[] = $servicesBout;
            $servicesBout->setBoutique($this);
        }

        return $this;
    }

    public function removeServicesBout(ServicesBout $servicesBout): self
    {
        if ($this->servicesBouts->removeElement($servicesBout)) {
            // set the owning side to null (unless already changed)
            if ($servicesBout->getBoutique() === $this) {
                $servicesBout->setBoutique(null);
            }
        }

        return $this;
    }

    public function getPieceIdentity(): ?string
    {
        return $this->pieceIdentity;
    }

    public function setPieceIdentity(?string $pieceIdentity): self
    {
        $this->pieceIdentity = $pieceIdentity;

        return $this;
    }
}
