<?php

namespace App\Entity;

use App\Repository\FlixbusRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FlixbusRepository::class)
 */
class Flixbus
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
    private $reservation_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_depart;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_arriver;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $station_depart;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $station_arriver;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $type_trajet;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_total;

    /**
     * @ORM\Column(type="float")
     */
    private $montant_service;

    /**
     * @ORM\Column(type="json")
     */
    private $nbre_passagers = [];

    /**
     * @ORM\ManyToOne(targetEntity=Bout::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $boutique;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $order_id;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $description = [];

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_resa;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $frais;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $frais_boutique;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservationId(): ?string
    {
        return $this->reservation_id;
    }

    public function setReservationId(string $reservation_id): self
    {
        $this->reservation_id = $reservation_id;

        return $this;
    }

    public function getDateDepart(): ?\DateTimeInterface
    {
        return $this->date_depart;
    }

    public function setDateDepart(\DateTimeInterface $date_depart): self
    {
        $this->date_depart = $date_depart;

        return $this;
    }

    public function getDateArriver(): ?\DateTimeInterface
    {
        return $this->date_arriver;
    }

    public function setDateArriver(\DateTimeInterface $date_arriver): self
    {
        $this->date_arriver = $date_arriver;

        return $this;
    }

    public function getStationDepart(): ?string
    {
        return $this->station_depart;
    }

    public function setStationDepart(string $station_depart): self
    {
        $this->station_depart = $station_depart;

        return $this;
    }

    public function getStationArriver(): ?string
    {
        return $this->station_arriver;
    }

    public function setStationArriver(string $station_arriver): self
    {
        $this->station_arriver = $station_arriver;

        return $this;
    }

    public function getTypeTrajet(): ?string
    {
        return $this->type_trajet;
    }

    public function setTypeTrajet(string $type_trajet): self
    {
        $this->type_trajet = $type_trajet;

        return $this;
    }

    public function getMontantTotal(): ?float
    {
        return $this->montant_total;
    }

    public function setMontantTotal(float $montant_total): self
    {
        $this->montant_total = $montant_total;

        return $this;
    }

    public function getMontantService(): ?float
    {
        return $this->montant_service;
    }

    public function setMontantService(float $montant_service): self
    {
        $this->montant_service = $montant_service;

        return $this;
    }

    public function getNbrePassagers(): ?array
    {
        return $this->nbre_passagers;
    }

    public function setNbrePassagers(array $nbre_passagers): self
    {
        $this->nbre_passagers = $nbre_passagers;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->order_id;
    }

    public function setOrderId(?string $order_id): self
    {
        $this->order_id = $order_id;

        return $this;
    }

    public function getDescription(): ?array
    {
        return $this->description;
    }

    public function setDescription(?array $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateResa(): ?\DateTimeInterface
    {
        return $this->date_resa;
    }

    public function setDateResa(?\DateTimeInterface $date_resa): self
    {
        $this->date_resa = $date_resa;

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
