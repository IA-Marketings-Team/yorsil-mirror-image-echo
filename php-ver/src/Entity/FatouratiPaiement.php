<?php

namespace App\Entity;

use App\Repository\FatouratiPaiementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FatouratiPaiementRepository::class)
 */
class FatouratiPaiement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=FatouratiCreanciers::class, inversedBy="fatouratiPaiements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creancier;

    /**
     * @ORM\ManyToOne(targetEntity=FatouratiCreances::class, inversedBy="fatouratiPaiements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creance;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $refTxSysPmt;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $refTxFatourati;

    /**
     * @ORM\Column(type="float")
     */
    private $montantTTC;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montant_devise;

    /**
     * @ORM\Column(type="json")
     */
    private $liste_article_selectionne = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_ajout;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_envoie_annulation;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $annulation;

    /**
     * @ORM\ManyToOne(targetEntity=TauxChange::class, inversedBy="fatouratiPaiements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $taux_change;

    /**
     * @ORM\ManyToOne(targetEntity=Bout::class, inversedBy="fatouratiPaiements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $boutique;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreancier(): ?FatouratiCreanciers
    {
        return $this->creancier;
    }

    public function setCreancier(?FatouratiCreanciers $creancier): self
    {
        $this->creancier = $creancier;

        return $this;
    }

    public function getCreance(): ?FatouratiCreances
    {
        return $this->creance;
    }

    public function setCreance(?FatouratiCreances $creance): self
    {
        $this->creance = $creance;

        return $this;
    }

    public function getRefTxSysPmt(): ?string
    {
        return $this->refTxSysPmt;
    }

    public function setRefTxSysPmt(string $refTxSysPmt): self
    {
        $this->refTxSysPmt = $refTxSysPmt;

        return $this;
    }

    public function getRefTxFatourati(): ?string
    {
        return $this->refTxFatourati;
    }

    public function setRefTxFatourati(string $refTxFatourati): self
    {
        $this->refTxFatourati = $refTxFatourati;

        return $this;
    }

    public function getMontantTTC(): ?float
    {
        return $this->montantTTC;
    }

    public function setMontantTTC(float $montantTTC): self
    {
        $this->montantTTC = $montantTTC;

        return $this;
    }

    public function getMontantDevise(): ?float
    {
        return $this->montant_devise;
    }

    public function setMontantDevise(?float $montant_devise): self
    {
        $this->montant_devise = $montant_devise;

        return $this;
    }

    public function getListeArticleSelectionne(): ?array
    {
        return $this->liste_article_selectionne;
    }

    public function setListeArticleSelectionne(array $liste_article_selectionne): self
    {
        $this->liste_article_selectionne = $liste_article_selectionne;

        return $this;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->date_ajout;
    }

    public function setDateAjout(\DateTimeInterface $date_ajout): self
    {
        $this->date_ajout = $date_ajout;

        return $this;
    }

    public function getDateEnvoieAnnulation(): ?\DateTimeInterface
    {
        return $this->date_envoie_annulation;
    }

    public function setDateEnvoieAnnulation(?\DateTimeInterface $date_envoie_annulation): self
    {
        $this->date_envoie_annulation = $date_envoie_annulation;

        return $this;
    }

    public function getAnnulation(): ?string
    {
        return $this->annulation;
    }

    public function setAnnulation(?string $annulation): self
    {
        $this->annulation = $annulation;

        return $this;
    }

    public function getTauxChange(): ?tauxChange
    {
        return $this->taux_change;
    }

    public function setTauxChange(?tauxChange $taux_change): self
    {
        $this->taux_change = $taux_change;

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
}
