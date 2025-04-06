<?php

namespace App\Entity;

use App\Repository\CreditRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CreditRepository::class)
 */
class Credit
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
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\ManyToOne(targetEntity=Bout::class, inversedBy="credits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bout;

    /**
     * @ORM\ManyToOne(targetEntity=Percept::class, inversedBy="credits")
     */
    private $percept;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="credits")
     * @ORM\JoinColumn(nullable=true)
     */
    private $admin;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isvalid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ref;

    /**
     * @ORM\OneToMany(targetEntity=Notificationrechargement::class, mappedBy="credit")
     */
    private $notificationrechargements;

    /**
     * @ORM\ManyToOne(targetEntity=Fichier::class, inversedBy="credits")
     */
    private $file;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isdelete;

    public function __construct()
    {
        $this->notificationrechargements = new ArrayCollection();
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

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
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

    public function getPercept(): ?Percept
    {
        return $this->percept;
    }

    public function setPercept(?Percept $percept): self
    {
        $this->percept = $percept;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getAdmin(): ?User
    {
        return $this->admin;
    }

    public function setAdmin(?User $admin): self
    {
        $this->admin = $admin;

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

    public function getIsvalid(): ?bool
    {
        return $this->isvalid;
    }

    public function setIsvalid(bool $isvalid): self
    {
        $this->isvalid = $isvalid;

        return $this;
    }

    public function getRef(): ?string
    {
        return $this->ref;
    }

    public function setRef(?string $ref): self
    {
        $this->ref = $ref;

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
            $notificationrechargement->setCredit($this);
        }

        return $this;
    }

    public function removeNotificationrechargement(Notificationrechargement $notificationrechargement): self
    {
        if ($this->notificationrechargements->removeElement($notificationrechargement)) {
            // set the owning side to null (unless already changed)
            if ($notificationrechargement->getCredit() === $this) {
                $notificationrechargement->setCredit(null);
            }
        }

        return $this;
    }

    public function getFile(): ?Fichier
    {
        return $this->file;
    }

    public function setFile(?Fichier $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getIsdelete(): ?bool
    {
        return $this->isdelete;
    }

    public function setIsdelete(?bool $isdelete): self
    {
        $this->isdelete = $isdelete;

        return $this;
    }
}
