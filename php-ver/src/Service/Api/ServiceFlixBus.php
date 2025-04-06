<?php

namespace App\Service\Api;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceFlixBus
{

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ContainerInterface $_container
    ) {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }

    // Fonction pour avoir l'access token de l'API
    public function getAccessToken()
    {
        $curl = curl_init();

        $data = array(
            "email" => "support@yorsil.com",
            "password" => "BNq@h6vzTvq8"
        );

        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'Content-Length: ' . strlen($postFields)
            ],
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/partner/authenticate",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction qui retourne toutes les villes dans FlixBus
    public function getCities()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862'
            ],
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/network/cities",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction qui retourne toutes les stations dans FlixBus
    public function getStations()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862'
            ],
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/network/stations",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction qui retourne toutes les voyages entre 2 villes à une date donné
    public function getTripSearch($_type_recherche, $_id_depart, $_id_arrive, $_date_depart, $_date_arrive, $_nbre_adult, $_nbre_child, $_bike)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862'
            ],
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/trip/search.json?search_by=" . $_type_recherche . "&from=" . $_id_depart . "&to=" . $_id_arrive . "&departure_date=" . $_date_depart . "&arrival_date=" . $_date_arrive . "&adult=" . $_nbre_adult . "&children=" . $_nbre_child . "&bikes=" . $_bike . "&locale=fr&currency=EUR",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction pour creer une reservation pour avoir id_reservation et reservation_token
    public function createReservation($_token, $_trip_uid, $_nbre_child, $_bike, $_nbre_adult)
    {
        $curl = curl_init();

        $data = array(
            "trip_uid" => $_trip_uid,
            "children" => $_nbre_child,
            "bikes" => $_bike,
            "currency" => "EUR",
            "adult" => $_nbre_adult,
        );

        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Inherit',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'X-API-Session: ' . $_token,
                'Content-Length: ' . strlen($postFields)
            ],
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/reservation/items.json",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction pour ajouter un trajet retour a la reservation
    // Utiliser en cas de trajet aller-retour
    public function addReturnTripReservation($_token, $_trip_uid, $_nbre_child, $_bike, $_nbre_adult, $_reservation_id, $_reservation_token)
    {
        $curl = curl_init();

        $data = array(
            "trip_uid" => $_trip_uid,
            "children" => $_nbre_child,
            "bikes" => $_bike,
            "currency" => "EUR",
            "adult" => $_nbre_adult,
            "reservation" => $_reservation_id,
            "reservation_token" => $_reservation_token
        );

        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Inherit',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'X-API-Session: ' . $_token,
                'Content-Length: ' . strlen($postFields)
            ],
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/reservation/items.json",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction qui retourne le details des passagers
    public function getPassengersDetails($_reservation_id, $_reservation_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862'
            ],
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/reservations/" . $_reservation_id . "/passengers.json?reservation_token=" . $_reservation_token,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction pour modifier le details de chaque passager
    public function putPassengersDetails($_reservation_id, $_reservation_token, $_passagers)
    {
        $curl = curl_init();

        // Préparer les données pour `x-www-form-urlencoded`
        $data = [
            'reservation_token' => $_reservation_token,
            'with_donation' => true,
            'donation_partner' => 'atmosfair',
        ];
        foreach ($_passagers as $index => $passenger) {
            $data["passengers[$index][firstname]"] = $passenger['firstname'];
            $data["passengers[$index][lastname]"]  = $passenger['lastname'];
            $data["passengers[$index][phone]"] = $passenger['phone'];
            $data["passengers[$index][birthdate]"]  = $passenger['birthdate'];
            $data["passengers[$index][type]"] = $passenger['type'];
            $data["passengers[$index][reference_id]"]  = $passenger['reference_id'];
        }

        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'Content-Length: ' . strlen($postFields)
            ],
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/reservations/" . $_reservation_id . "/passengers.json",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction qui retourne des offres supplemenetaires ou annexes 
    public function getAncillaryOffers($_reservation_id, $_reservation_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862'
            ],
            CURLOPT_URL => "https://global.api.flixbus.com/public/v2/reservations/" . $_reservation_id . "/ancillaries.json?reservation_token=" . $_reservation_token,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction pour ajouter une offre supplemenetaire ou annexe sur un voyage 
    public function putAncillaryOffers($_token, $_reservation_id, $_reservation_token, $_reference_id, $_product_type, $_amount)
    {
        $curl = curl_init();

        $data = [
            "purchaseRequests" => [
                [
                    "ancillary_offer_reference_id" => $_reference_id,
                    "product_type" => $_product_type,
                    "amount" => $_amount
                ]
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Inherit',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'X-API-Session: ' . $_token,
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://global.api.flixbus.com/public/v2/reservations/" . $_reservation_id . "/ancillaries?reservation_token=" . $_reservation_token,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction pour un echange de bon
    public function redeemVoucher($_token, $_reservation_id, $_reservation_token, $_code)
    {
        $curl = curl_init();

        $data = array(
            "reservation_token" => $_reservation_token,
            "code" => $_code
        );

        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Inherit',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'X-API-Session: ' . $_token,
                'Content-Length: ' . strlen($postFields)
            ],
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/reservations/" . $_reservation_id . "/vouchers.json",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction qui retourne la methode de paiement
    public function getPaymentMethods($_token, $_reservation_id, $_reservation_token)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Inherit',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'X-API-Session: ' . $_token,
            ],
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/payment/list.json?reservation=" . $_reservation_id . "&reservation_token=" . $_reservation_token,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction pour demarrer un paiement
    public function startPayment($_token, $_reservation_id, $_reservation_token, $_email, $_psp, $_method)
    {
        $curl = curl_init();

        $data = array(
            "reservation" => $_reservation_id,
            "reservation_token" => $_reservation_token,
            "email" => $_email,
            "payment[psp]" => $_psp,
            "payment[method]" => $_method
        );

        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Inherit',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'X-API-Session: ' . $_token,
                'Content-Length: ' . strlen($postFields)
            ],
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/payment/start.json",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction pour finaliser un paiement
    public function finalizePaymentBooking($_token, $_reservation_id, $_reservation_token, $_payment_id)
    {
        $curl = curl_init();

        $data = array(
            "reservation" => $_reservation_id,
            "reservation_token" => $_reservation_token,
            "payment_id" => $_payment_id,
        );

        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Inherit',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'X-API-Session: ' . $_token,
                'Content-Length: ' . strlen($postFields)
            ],
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_URL => "https://global.api.flixbus.com/public/v1/payment/commit.json",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction pour recuperer le ticket de voyage
    public function ticketForOrder($_token, $_order_id, $_download_hash)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Inherit',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'X-API-Session: ' . $_token
            ],
            CURLOPT_URL => "https://global.api.flixbus.com/public/v2/orders/" . $_order_id . "/info.json?download_hash=" . $_download_hash,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_TIMEOUT => 120
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }

    // Fonction pour annuler le ticket de voyage
    public function cancelOrder($_token, $_order_id, $_download_hash)
    {
        $curl = curl_init();
        $data = [
            "refundType" => "cash",
            "context"    => "Following tickets are being canceled due to lack of capacity."
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Inherit',
                'X-API-Authentication: 7c987cd015172618cb0669cd5e48e862',
                'X-API-Session: ' . $_token
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://global.api.flixbus.com/order/v3/orders/" . $_order_id . "/partner/cancel?token=" . $_download_hash,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_TIMEOUT => 120
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        $is_error = null;

        if ($error) {
            $is_error = $error;
        } else {
        }
        return [$response, $is_error];
    }
}
