<?php

namespace App\Entity;

use App\Repository\GrilleTarifaireBoutiqueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GrilleTarifaireBoutiqueRepository::class)
 */
class GrilleTarifaireBoutique
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=GrilleTarifaire::class, inversedBy="grilleTarifaireBoutiques")
     * @ORM\JoinColumn(nullable=false)
     */
    private $grille_tarifaire;

    /**
     * @ORM\ManyToOne(targetEntity=bout::class, inversedBy="grilleTarifaireBoutiques")
     * @ORM\JoinColumn(nullable=false)
     */
    private $boutique;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_ajout;

    /**
     * @ORM\Column(type="float")
     */
    private $comm_yorsil;

    /**
     * @ORM\Column(type="float")
     */
    private $comm_distrib;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrilleTarifaire(): ?GrilleTarifaire
    {
        return $this->grille_tarifaire;
    }

    public function setGrilleTarifaire(?GrilleTarifaire $grille_tarifaire): self
    {
        $this->grille_tarifaire = $grille_tarifaire;

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

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(?\DateTimeInterface $date_ajout): self
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }

    public function getCommYorsil(): ?float
    {
        return $this->comm_yorsil;
    }

    public function setCommYorsil(float $comm_yorsil): self
    {
        $this->comm_yorsil = $comm_yorsil;

        return $this;
    }

    public function getCommDistrib(): ?float
    {
        return $this->comm_distrib;
    }

    public function setCommDistrib(float $comm_distrib): self
    {
        $this->comm_distrib = $comm_distrib;

        return $this;
    }
}
