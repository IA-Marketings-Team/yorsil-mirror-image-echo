<?php

namespace App\Controller\Service;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TimingController extends AbstractController
{
    /**
     * @Route("/verif-time", name="verif_time")
     */
    public function verifTime()
    {
        // Fuseau horaire du serveur (UTC)
        $serverTimezone = new \DateTimeZone(date_default_timezone_get());
        $serverTime = new \DateTime('now', $serverTimezone);

        // Fuseau horaire local (Pacific/Honolulu)
        $localTimezone = new \DateTimeZone('Europe/Berlin');
        $localTime = new \DateTime('now', $localTimezone);

        // Obtenir l'écart de décalage entre UTC et Pacific/Honolulu en secondes
        $serverOffset = $serverTime->getOffset();
        $localOffset = $localTime->getOffset();

        // Calculer la différence en secondes entre les deux fuseaux horaires
        $offsetDifferenceInSeconds = abs($serverOffset - $localOffset);

        // Convertir l'écart en heures, minutes et secondes
        $hours = floor($offsetDifferenceInSeconds / 3600);
        $minutes = floor(($offsetDifferenceInSeconds % 3600) / 60);
        $seconds = $offsetDifferenceInSeconds % 60;

        // Format de l'intervalle en HH:MM:SS
        $intervalTime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

        return new JsonResponse([
            "serverTime" => $serverTime->format('Y-m-d H:i:s'),
            "serverTimezone" => $serverTimezone->getName(),
            "localTime" => $localTime->format('Y-m-d H:i:s'),
            "intervalTime" => $intervalTime
        ]);
    }
}
