<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\User;
use App\Repository\BoutRepository;
use App\Repository\ServicesBoutRepository;

class ServicesExtension extends AbstractExtension
{
    private $boutRepo;
    private $serviceBoutRepo;

    public function __construct(
        BoutRepository $boutRepo,
        ServicesBoutRepository $serviceBoutRepo)
    {
        $this->boutRepo        = $boutRepo;
        $this->serviceBoutRepo = $serviceBoutRepo;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('serviceBoutique', [$this, 'getServiceBoutique']),
            new TwigFunction('formatDate', [$this, 'formatDate']),
        ];
    }

    public function getServiceBoutique(User $user){
        $bout    = $this->boutRepo->findOneByUser($user);
        $service = $this->serviceBoutRepo->findOneByBoutique($bout);
        return $service;
    }

    public function formatDate(string $dateString): string
    {
        // Convertir la date en objet DateTime
        $date = \DateTime::createFromFormat('d.m.Y', $dateString);

        // Vérification si la date est valide
        if (!$date) {
            return 'Date invalide';
        }

        // Jours de la semaine
        $daysOfWeek = ["di", "lu", "ma", "me", "je", "ve", "sa"];

        // Mois de l'année
        $months = ["janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre"];

        // Récupération des valeurs
        $dayOfWeek = $daysOfWeek[(int)$date->format('w')];
        $dayOfMonth = (int)$date->format('d');
        $month = $months[(int)$date->format('n') - 1];

        // Retourner la date formatée
        return sprintf('%s, %d %s', $dayOfWeek, $dayOfMonth, $month);
    }
    
}