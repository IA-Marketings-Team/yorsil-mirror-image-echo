<?php

namespace App\Service;

use App\DTO\FlixBusSearchRequest;
use App\Manager\FlixBusManager;

class RoundTripSearchService extends FlixBusSearchService
{
    public function searchRoundTrips(FlixBusSearchRequest $request): array
    {
        // Validation spécifique aux allers-retours
        if ($request->isRoundTrip && !$request->returnDate) {
            return [
                'error' => 'La date de retour est obligatoire pour un aller-retour',
                'code' => 400
            ];
        }

        // Recherche aller
        $outboundResults = parent::searchTrips($request);

        if (!empty($outboundResults['error'])) {
            return $outboundResults;
        }

        // Préparation recherche retour
        $returnRequest = clone $request;
        $returnRequest->departId = $request->arriveId;
        $returnRequest->arriveId = $request->departId;
        $returnRequest->departDate = $request->returnDate;
        $returnRequest->isRoundTrip = false; // Pour éviter la récursion

        // Recherche retour
        $returnResults = parent::searchTrips($returnRequest);

        return [
            'outbound' => $outboundResults,
            'return' => $returnResults,
        ];
    }
}
