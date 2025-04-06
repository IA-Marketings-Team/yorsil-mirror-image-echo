<?php

namespace App\Service\Metier;

use App\Entity\Flixbus;
use App\Entity\Recharge;
use App\Entity\Transfert;
use App\Entity\Rechargeflexi;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\KernelInterface;

class ServiceCA
{
    public function formatCA($entities): array
    {
        $_ca_items = [];
        $totalAmount = 0; // Initialiser le total
        $_ca_flix = 0;
        $_ca_recharge = 0;
        $_ca_transfert = 0;

        foreach ($entities as $entity) {
            if ($entity instanceof Flixbus) {
                $_ca = ($entity->getMontant() * ($entity->getFrais()/100)) - $entity->getFraisBoutique();

                $_ca_items[] = [
                    'id' => $entity->getId(),
                    'type' => 'Flixbus',
                    'date' => $entity->getDateResa(),
                    'ca' => $_ca,
                    'ca_boutique' => $entity->getFraisBoutique()
                ];

                $totalAmount += $_ca; // Ajouter au total
                $_ca_flix += $_ca ; // CA flix
            } elseif ($entity instanceof Transfert) {
                $_ca = ($entity->getMontant() * ($entity->getCommission()/100)) - $entity->getFraisBoutique();

                $_ca_items[] = [
                    'id' => $entity->getId(),
                    'type' => 'Transfert',
                    'date' => $entity->getDate(),
                    'ca' => $_ca,
                    'ca_boutique' => $entity->getFraisBoutique()
                ];

                $totalAmount += $_ca; 
                $_ca_transfert += $_ca;
            } elseif ($entity instanceof Recharge) {
                $_ca = ($entity->getMontant() * ($entity->getFrais()/100)) - $entity->getFraisBoutique();

                $_ca_items[] = [
                    'id' => $entity->getId(),
                    'type' => 'Recharge',
                    'date' => $entity->getSaleDate(),
                    'ca' => $_ca,
                    'ca_boutique' => $entity->getFraisBoutique()
                ];

                $totalAmount += $_ca; 
                $_ca_recharge += $_ca;
            } elseif ($entity instanceof Rechargeflexi) {
                $_ca = ($entity->getMontant() * ($entity->getFrais()/100)) - $entity->getFraisBout();

                $_ca_items[] = [
                    'id' => $entity->getId(),
                    'type' => 'Transfert',
                    'date' => $entity->getDate(),
                    'ca' => $_ca,
                    'ca_boutique' => $entity->getFraisBout()
                ];

                $totalAmount += $_ca; 
                $_ca_transfert += $_ca;
            }
        }

        return [
            'items' => $_ca_items,
            'total' => $totalAmount,
            'ca_flix' => $_ca_flix,
            'ca_recharge' => $_ca_recharge,
            'ca_transfert' => $_ca_transfert
        ];
    }

    public function formatBoutiqueCA($entities): array
    {
        $_ca_items = [];
        $totalAmount = 0; // Initialiser le total
        $_ca_flix = 0;
        $_ca_recharge = 0;
        $_ca_transfert = 0;

        foreach ($entities as $entity) {
            if ($entity instanceof Flixbus) {
                $_ca = $entity->getFraisBoutique();

                $_ca_items[] = [
                    'id' => $entity->getId(),
                    'type' => 'Flixbus',
                    'date' => $entity->getDateResa(),
                    'ca' => $_ca,
                    'ca_boutique' => $entity->getFraisBoutique()
                ];

                $totalAmount += $_ca; // Ajouter au total
                $_ca_flix += $_ca ; // CA flix
            } elseif ($entity instanceof Transfert) {
                $_ca = $entity->getFraisBoutique();

                $_ca_items[] = [
                    'id' => $entity->getId(),
                    'type' => 'Transfert',
                    'date' => $entity->getDate(),
                    'ca' => $_ca,
                    'ca_boutique' => $entity->getFraisBoutique()
                ];

                $totalAmount += $_ca; 
                $_ca_transfert += $_ca;
            } elseif ($entity instanceof Recharge) {
                $_ca = $entity->getFraisBoutique();

                $_ca_items[] = [
                    'id' => $entity->getId(),
                    'type' => 'Recharge',
                    'date' => $entity->getSaleDate(),
                    'ca' => $_ca,
                    'ca_boutique' => $entity->getFraisBoutique()
                ];

                $totalAmount += $_ca; 
                $_ca_recharge += $_ca;
            } elseif ($entity instanceof Rechargeflexi) {
                $_ca = $entity->getFraisBout();

                $_ca_items[] = [
                    'id' => $entity->getId(),
                    'type' => 'Transfert',
                    'date' => $entity->getDate(),
                    'ca' => $_ca,
                    'ca_boutique' => $entity->getFraisBout()
                ];

                $totalAmount += $_ca; 
                $_ca_transfert += $_ca;
            }
        }

        return [
            'items' => $_ca_items,
            'total' => $totalAmount,
            'ca_flix' => $_ca_flix,
            'ca_recharge' => $_ca_recharge,
            'ca_transfert' => $_ca_transfert
        ];
    }

}