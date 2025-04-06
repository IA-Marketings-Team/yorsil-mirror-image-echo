<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FichierRepository::class)
 */
class Fichier
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
    private $url_fichier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom_fichier;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $ext_fichier;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nat_fichier;

    /**
     * @ORM\OneToMany(targetEntity=Operateur::class, mappedBy="logo")
     */
    private $operateurs;

    /**
     * @ORM\OneToMany(targetEntity=Credit::class, mappedBy="file")
     */
    private $credits;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="file")
     */
    private $depots;

    public function __construct()
    {
        $this->operateurs = new ArrayCollection();
        $this->credits = new ArrayCollection();
        $this->depots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlFichier(): ?string
    {
        return $this->url_fichier;
    }

    public function setUrlFichier(string $url_fichier): self
    {
        $this->url_fichier = $url_fichier;

        return $this;
    }

    public function getNomFichier(): ?string
    {
        return $this->nom_fichier;
    }

    public function setNomFichier(string $nom_fichier): self
    {
        $this->nom_fichier = $nom_fichier;

        return $this;
    }

    public function getExtFichier(): ?string
    {
        return $this->ext_fichier;
    }

    public function setExtFichier(string $ext_fichier): self
    {
        $this->ext_fichier = $ext_fichier;

        return $this;
    }

    public function getNatFichier(): ?string
    {
        return $this->nat_fichier;
    }

    public function setNatFichier(string $nat_fichier): self
    {
        $this->nat_fichier = $nat_fichier;

        return $this;
    }

    /**
     * @return Collection|Operateur[]
     */
    public function getOperateurs(): Collection
    {
        return $this->operateurs;
    }

    public function addOperateur(Operateur $operateur): self
    {
        if (!$this->operateurs->contains($operateur)) {
            $this->operateurs[] = $operateur;
            $operateur->setLogo($this);
        }

        return $this;
    }

    public function removeOperateur(Operateur $operateur): self
    {
        if ($this->operateurs->removeElement($operateur)) {
            // set the owning side to null (unless already changed)
            if ($operateur->getLogo() === $this) {
                $operateur->setLogo(null);
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
            $credit->setFile($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): self
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getFile() === $this) {
                $credit->setFile(null);
            }
        }

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
            $depot->setFile($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getFile() === $this) {
                $depot->setFile(null);
            }
        }

        return $this;
    }
}
