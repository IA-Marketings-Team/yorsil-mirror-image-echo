<?php

namespace App\Service;

use App\DTO\FlixBusSearchRequest;
use App\Service\Api\ServiceFlixBus;

class FlixBusSearchService
{
    private ServiceFlixBus $flixBusManager;

    public function __construct(ServiceFlixBus $flixBusManager)
    {
        $this->flixBusManager = $flixBusManager;
    }

    public function searchTrips(FlixBusSearchRequest $request): array
    {
        $trips = $this->flixBusManager->getTripSearch(
            $request->type,
            $request->departId,
            $request->arriveId,
            $request->departDate,
            $request->arriveDate,
            $request->adults,
            $request->children,
            $request->bikes
        );

        if ($trips[1]) {
            return [
                'error' => 'Erreur lors de la recherche de voyages',
                'code' => 500
            ];
        }

        $list = json_decode($trips[0]);

        $currentDate = new \DateTime($request->departDate);
        $nextDate = false;
        $attempts = 0;

        while (empty($list->trips) && $attempts < 5) {
            $dateDepart = $currentDate->format('d.m.Y');
            $trips = $this->flixBusManager->getTripSearch(
                $request->type,
                $request->departId,
                $request->arriveId,
                $dateDepart,
                $request->arriveDate,
                $request->adults,
                $request->children,
                $request->bikes
            );
            $list = ($trips[1]) ? null : json_decode($trips[0]);

            if (empty($list->trips)) {
                $currentDate->modify('+1 day');
                $nextDate = true;
            }
            
            $attempts++;
        }

        return [
            'trips' => $list,
            'passport' => $request->passport,
            'suggest' => [
                'next' => $nextDate,
                'emptyDate' => $request->departDate,
                'date' => $currentDate->format('d.m.Y'),
            ]
        ];
    }
}
