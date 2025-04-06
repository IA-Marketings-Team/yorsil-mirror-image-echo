<?php

namespace App\Service\Metier;

use App\Entity\Flixbus;
use App\Entity\Recharge;
use App\Entity\RechargeFlexi;
use App\Entity\Transfert;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\KernelInterface;

class PdfService
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }


    public function dispatcherFacturesParMontant(array $rechargements, $code, $seuil = 1000)
    {
        $factures = [];
        $currentFacture = [];
        $totalFacture = 0;
        $numFacture = 1;

        foreach ($rechargements as $rechargement) {
            $montantCredit = $rechargement['montant'];

            while ($montantCredit > 0) {
                // Si le total courant + le montant du crédit dépasse le seuil
                if ($totalFacture + $montantCredit > $seuil) {
                    // Calcule le montant à ajouter pour atteindre le seuil
                    $montantAJouter = $seuil - $totalFacture;

                    // Créer une facture avec le montant ajusté
                    $factures[] = [
                        'ref' => 'FACT'. $code .'-'.str_pad($numFacture, 3, '0', STR_PAD_LEFT),
                        'total' => $seuil,
                        'rechargements' => array_merge($currentFacture, [
                            [
                                'id' => $rechargement['id'],
                                'date' => $rechargement['date'],
                                'montant' => $montantAJouter,
                                'type' => $rechargement['type'],
                                'ref' => $rechargement['ref'],
                            ],
                        ]),
                    ];

                    // Réinitialiser pour la prochaine facture
                    $currentFacture = [];
                    $totalFacture = 0;
                    $numFacture++;

                    // Réduire le montant du crédit restant
                    $montantCredit -= $montantAJouter;
                } else {
                    // Ajouter le crédit à la facture courante
                    $currentFacture[] = [
                        'id' => $rechargement['id'],
                        'date' => $rechargement['date'],
                        'montant' => $montantCredit,
                        'type' => $rechargement['type'],
                        'ref' => $rechargement['ref'],
                    ];

                    $totalFacture += $montantCredit;
                    $montantCredit = 0; // Tout le montant a été utilisé
                }
            }
        }

        // Si des crédits restent après la dernière facture
        if (!empty($currentFacture)) {
            $factures[] = [
                'ref' => 'FACT'. $code .'-'.str_pad($numFacture, 3, '0', STR_PAD_LEFT),
                'total' => $totalFacture,
                'rechargements' => $currentFacture,
            ];
        }

        return $factures;
    }


     public function formatForBilling($entities): array
    {
        $billingItems = [];
        $totalAmount = 0; // Initialiser le total

        foreach ($entities as $entity) {
            if ($entity instanceof Flixbus) {
                $montant = ($entity->getMontantTotal() - $entity->getFraisBoutique());

                $billingItems[] = [
                    'id' => $entity->getId(),
                    'type' => 'Flixbus',
                    'nomservice' => 'Trajet ' . $entity->getStationDepart() . ' - ' . $entity->getStationArriver(),
                    'reference' => $entity->getOrderId(),
                    'date' => $entity->getDateResa(),
                    'description' => 'Trajet ' . $entity->getStationDepart() . ' - ' . $entity->getStationArriver(),
                    'montant' => $montant,
                    'email' => $entity->getEmail(),
                    'tva' => null,
                    'commission' => $entity->getFraisBoutique()
                ];

                $totalAmount += $montant; // Ajouter au total
            } elseif ($entity instanceof Recharge) {
                // Calcul du montant TTC incluant la TVA à 20%
                $montantHT = $entity->getMontant();
                $tva = $entity->getTva();
                $montantTTC = $montantHT;

                if ($tva !== null && $tva > 0) {
                    $montantTTC = ($montantHT - $entity->getFraisBoutique()) / (1+($tva/100));
                } else {
                    $montantTTC = ($montantHT - $entity->getFraisBoutique()) ;
                }

                $billingItems[] = [
                    'id' => $entity->getId(),
                    'type' => 'Recharge',
                    'nomservice' => $entity->getproductInformations()['productInformations']['description'],
                    'reference' => $entity->getSaleRef(),
                    'date' => $entity->getSaleDate(),
                    'description' => 'Recharge de ' . $entity->getQty() . ' unité(s)',
                    'montant' => $montantTTC,
                    'email' => null,
                    'tva' => $entity->getTva(),
                    'commission' => $entity->getFraisBoutique()
                ];

                $totalAmount += $montantTTC; // Ajouter au total
            } elseif ($entity instanceof Transfert) {
                $montant = ($entity->getMontant()-$entity->getFraisBoutique());

                $billingItems[] = [
                    'id' => $entity->getId(),
                    'type' => 'Transfert',
                    'nomservice' => 'Transfert de '.$montant. ' €',
                    'reference' => $entity->getTrxId(),
                    'date' => $entity->getDate(),
                    'description' => $entity->getDescription(),
                    'montant' => $montant,
                    'email' => $entity->getEmail(),
                    'tva' => null,
                    'commission' => $entity->getFraisBoutique()
                ];

                $totalAmount += $montant; // Ajouter au total
            } elseif ($entity instanceof RechargeFlexi) {
                $montant = ($entity->getMontant()-$entity->getFraisBout());

                $billingItems[] = [
                    'id' => $entity->getId(),
                    'type' => 'Transfert',
                    'nomservice' => 'Transfert de '.$montant. ' €',
                    'reference' => "",
                    'date' => $entity->getDate(),
                    'description' => "",
                    'montant' => $montant,
                    'email' => "",
                    'tva' => null,
                    'commission' => $entity->getFraisBout()
                ];

                $totalAmount += $montant; // Ajouter au total
            }

        }

        // Trier les éléments par date (le plus récent en premier)
        usort($billingItems, function ($a, $b) {
            return $b['date'] <=> $a['date']; // Comparaison descendante
        });

        // Transformer les dates en chaîne pour l'affichage
        foreach ($billingItems as &$item) {
            if ($item['date'] instanceof \DateTime) {
                $item['date'] = $item['date']->format('Y-m-d H:i:s');
            }
        }

        // Ajouter le total au résultat
        return [
            'items' => $billingItems,
            'total' => $totalAmount
        ];
    }

    public function getTypeService(String $a = null, String $b = null, String $c = null){
        $_resultat = '';
        if ($a){ $_resultat = $a;}
        if ($b){ $_resultat = $b;}
        if ($c){ $_resultat = $c;}
        return $_resultat;
    }

    public function formatForBillingCA($entities): array
    {
        $totals = [];

        foreach ($entities as $entity) {
            if ($entity instanceof Flixbus) {
                $montant = $entity->getMontantTotal();
                $type = 'Flixbus';
            } elseif ($entity instanceof Recharge) {
                $montantHT = $entity->getMontant();
                $tva = $entity->getTva();
                $montant = $montantHT;
                $type = 'Recharge';
            } elseif ($entity instanceof Transfert || $entity instanceof RechargeFlexi) {
                // Fusionner Transfert et RechargeFlexi sous un seul type
                $montant = $entity instanceof Transfert ? $entity->getMontant() : $entity->getMontant();
                $type = 'Transfert de crédit';    
            } else {
                continue;
            }

            // Ajouter au total par type
            if (!isset($totals[$type])) {
                $totals[$type] = 0;
            }
            $totals[$type] += $montant;
        }

        // Formatter le résultat
        $result = [];
        foreach ($totals as $type => $total) {
            $result[] = [
                'type' => $type,
                'montant_total' => $total
            ];
        }

        return $result;
    }

}
