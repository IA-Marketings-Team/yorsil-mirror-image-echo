<?php

namespace App\Entity;

use App\Repository\PerceptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PerceptRepository::class)
 */
class Percept
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $createur;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $tele;

    /**
     * @ORM\OneToMany(targetEntity=Bout::class, mappedBy="percept")
     */
    private $bouts;

    /**
     * @ORM\OneToMany(targetEntity=Credit::class, mappedBy="percept")
     */
    private $credits;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $compte;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="percepteur")
     */
    private $depots;

    /**
     * @ORM\OneToMany(targetEntity=Seuilpercepteur::class, mappedBy="percepteur")
     */
    private $seuilpercepteurs;

    public function __construct()
    {
        $this->bouts = new ArrayCollection();
        $this->credits = new ArrayCollection();
        $this->depots = new ArrayCollection();
        $this->seuilpercepteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getCreateur(): ?string
    {
        return $this->createur;
    }

    public function setCreateur(?string $createur): self
    {
        $this->createur = $createur;

        return $this;
    }

    public function getTele(): ?string
    {
        return $this->tele;
    }

    public function setTele(?string $tele): self
    {
        $this->tele = $tele;

        return $this;
    }

    /**
     * @return Collection|Bout[]
     */
    public function getBouts(): Collection
    {
        return $this->bouts;
    }

    public function addBout(Bout $bout): self
    {
        if (!$this->bouts->contains($bout)) {
            $this->bouts[] = $bout;
            $bout->setPercept($this);
        }

        return $this;
    }

    public function removeBout(Bout $bout): self
    {
        if ($this->bouts->removeElement($bout)) {
            // set the owning side to null (unless already changed)
            if ($bout->getPercept() === $this) {
                $bout->setPercept(null);
            }
        }

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
            $credit->setPercept($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): self
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getPercept() === $this) {
                $credit->setPercept(null);
            }
        }

        return $this;
    }

    public function getCompte(): ?User
    {
        return $this->compte;
    }

    public function setCompte(?User $compte): self
    {
        $this->compte = $compte;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setPercepteur($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getPercepteur() === $this) {
                $depot->setPercepteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Seuilpercepteur[]
     */
    public function getSeuilpercepteurs(): Collection
    {
        return $this->seuilpercepteurs;
    }

    public function addSeuilpercepteur(Seuilpercepteur $seuilpercepteur): self
    {
        if (!$this->seuilpercepteurs->contains($seuilpercepteur)) {
            $this->seuilpercepteurs[] = $seuilpercepteur;
            $seuilpercepteur->setPercepteur($this);
        }

        return $this;
    }

    public function removeSeuilpercepteur(Seuilpercepteur $seuilpercepteur): self
    {
        if ($this->seuilpercepteurs->removeElement($seuilpercepteur)) {
            // set the owning side to null (unless already changed)
            if ($seuilpercepteur->getPercepteur() === $this) {
                $seuilpercepteur->setPercepteur(null);
            }
        }

        return $this;
    }

}
