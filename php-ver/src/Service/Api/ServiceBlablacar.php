<?php

namespace App\Service\Api;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceBlablacar
{
    private $_entity_manager;
    private $_container;
    private $_username = 'yorsil.preprod';
    private $_password = '61(@s@Hmdd4K';
    private $_client_id  = '474ae72thzcj2ob97v99zdm205h38y18254ow7xqhgiendu86x';
    private $_secret_key = 'NDc0YWU3MnRoemNqMm9iOTd2OTl6ZG0yMDVoMzh5MTgyNTRvdzd4cWhnaWVuZHU4Nng6cjBsdjgyOWl1MjlxdHB0M3NqN2g3YzEwYmkzMnIwdTA2bnN1NTFjeXc5Y2o1aGN1bjY=';
    
    public function __construct(
        EntityManagerInterface $_entity_manager,
        ContainerInterface $_container
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }


    public function getAccessToken(){
        $curl = curl_init();

        $data = array(
            "grant_type" => "https://com.sqills.s3.oauth.agent",
            "username" => $this->_username,
            "password" => $this->_password 
        );
         
        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Basic NDc0YWU3MnRoemNqMm9iOTd2OTl6ZG0yMDVoMzh5MTgyNTRvdzd4cWhnaWVuZHU4Nng6cjBsdjgyOWl1MjlxdHB0M3NqN2g3YzEwYmkzMnIwdTA2bnN1NTFjeXc5Y2o1aGN1bjY=",
                "Accept: application/json",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://api.bus.blablacar-acc.cloud.sqills.com/oauth/v2/token",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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
        return [$response,$is_error];
    }

    // Fonction qui retourne les stations dans Blablacar
    public function getStationsBlablacar($token){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept-Language: fr-FR"
            ],
            CURLOPT_URL => "https://api.bus.blablacar-acc.cloud.sqills.com/api/v2/meta/stations",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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

    // Fonction qui retourne les trajets aller/retour par code stations ou coordonnees geographique dans Blablacar
    public function postOrientation($token, $trajet){

        $curl = curl_init();

        // aller (outbound)
        $data = [
            "currency" => "EUR",
            "passengers"=> [
                [
                    "type"=> "A"
                ]
            ],
            "travels"=> [
                [
                    "origin"=> "NAN",
                    "destination"=> "PAR",
                    "direction"=> "outbound",
                    "departure"=> "2025-02-28"
                ]
            ]
        ];

        // aller-retour (outbound-inbound)
        // $data = [
        //     "currency" => "EUR",
        //     "passengers"=> [
        //         [
        //             "type"=> "A"
        //         ],
        //         [
        //             "type"=> "Y"
        //         ]
        //     ],
        //     "travels"=> [
        //         [
        //             "origin"=> "NAN",
        //             "destination"=> "PAR",
        //             "direction"=> "outbound",
        //             "departure"=> "2025-02-28"
        //         ],
        //         [
        //             "origin"=> "PAR",
        //             "destination"=> "NAN",
        //             "direction"=> "outbound",
        //             "departure"=> "2025-03-02"
        //         ]
        //     ]
        // ];

        // coordonee geographique
        // $data = [
        //     "currency" => "EUR",
        //     "passengers"=> [
        //         [
        //             "type"=> "A"
        //         ],
        //         [
        //             "type"=> "Y"
        //         ]
        //     ],
        //     "travels"=> [
        //         [   
        //             "origin_latitude"=> 48.86250394726548,
        //             "origin_longitude"=> 2.351040709741522,
        //             "origin_range_meters"=> 50000,
        //             "destination_latitude"=> 48.28709900567799,
        //             "destination_longitude"=> 11.68274779958021,
        //             "destination_range_meters"=> 50000,
        //             "direction"=> "outbound",
        //             "departure"=> "2025-02-26"
        //         ]
        //     ]
        // ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept-Language: fr-FR"
            ],
            CURLOPT_POSTFIELDS => json_encode($trajet),
            CURLOPT_URL => "https://api.bus.blablacar-acc.cloud.sqills.com/api/v2/orientation/journey-search",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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

     // Fonction pour creer une reservation dans Blablacar
    public function postCreateBooking($token, $infos){

        $curl = curl_init();
        

        flush(); // Force l'affichage immédiat
        // aller (outbound)
        $data = [
            "segments"=> [
                [
                    "client_ref"=> "travel_1",
                    "origin"=> "QJZ",  
                    "destination"=> "XPB",  
                    "direction"=> "outbound",  
                    "service_name"=> "8080",  
                    "service_identifier"=> "0:RDAOD|8080|CASTEL|2025-02-28|2025-02-28T07:45|2025-02-28T13:25|QJZ|XPB",  
                    "start_validity_date"=> "2025-02-28",
                    "items"=> [
                        [
                            "client_ref"=> "3e0d7d4d-ba4b-2eb0-9db4-0d01a0151928",
                            "passenger_id"=> "passenger_1",
                            "tariff_code"=> "ST4"
                        ]
                    ]
                ]
            ],
            "customer"=> [
                "first_name"=> "Freduciot",
                "last_name"=> "AMBY",
                "email"=> "freduciotamby@gmail.com",
                "birth_date"=> "2000-06-15"
            ],
            "passengers"=> [
                [
                    "client_ref"=> "passenger_ref_1",
                    "type"=> "A",
                    "disability_type"=> "NH",
                    "id"=> "passenger_1",
                    "first_name"=> "Freduciot",
                    "last_name"=> "AMBY",
                    "birth_date"=> "2000-06-15"
                ]
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept-Language: fr-FR"
            ],
            CURLOPT_POSTFIELDS => json_encode($infos),
            CURLOPT_URL => "https://api.bus.blablacar-acc.cloud.sqills.com/api/v2/booking",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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

    // Modification partielle de l'attribut customer (Facultatif)
    public function patchCustomer($token,$_booking_number){        
        $curl = curl_init();

        $data = [
            "customer"=> [
                "first_name"=> "Freduciot",
                "last_name"=> "AMBY",
                "birth_date"=> "2000-06-15",
                "email"=> "freduciotamby@gmail.com",
                "phone_number"=> "+261323456780",
                "address"=> [
                    "street"=> "Palatijn",
                    "house_number"=> "3",
                    "postal_code"=> "1234AA",
                    "city"=> "Enschede",
                    "country_code"=> "NL"
                ]
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept-Language: fr-FR"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://api.bus.blablacar-acc.cloud.sqills.com/api/v2/booking/".$_booking_number."/customer",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => "PATCH",
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

    // Modification partielle de l'attribut passengers (Facultatif)
    public function patchPassengers($token,$_booking_number){        
        $curl = curl_init();

        // 1 ou plusieurs passengers selon le nombre de passagers sur la reservation
        $data = [
            "passengers"=> [
                [
                    "id"=> "824ae820-b351-33c5-8958-de882d4138fd",
                    "type"=> "A",
                    "first_name"=> "Freduciot",
                    "last_name"=> "AMBY",
                    "email"=> "freduciotamby@gmail.com",
                    "phone_number"=> "+261323456780",
                    "birth_date"=> "2000-06-15"
                ]
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept-Language: fr-FR"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://api.bus.blablacar-acc.cloud.sqills.com/api/v2/booking/".$_booking_number."/passengers",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => "PATCH",
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

    // Fonction pour creer un paiement pour la reservation
    public function createPayment($token, $data){
        $_booking_number = $data["booking_number"];

        $curl = curl_init();

        // $data = [
        //     "payments"=> [
        //         [
        //             "amount"=> 17.99,
        //             "currency"=> "EUR",
        //             "method"=> "APIPAYMENT",
        //             "status"=> "S"
        //         ]
        //     ]
        // ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept-Language: fr-FR",
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://api.bus.blablacar-acc.cloud.sqills.com/api/v2/booking/".$_booking_number."/payments",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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

    // Fonction pour confirmer la reservation
    public function confirmBooking($token,$_booking_number){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept-Language: fr-FR"
            ],
            CURLOPT_URL => "https://api.bus.blablacar-acc.cloud.sqills.com/api/v2/booking/".$_booking_number."/confirm",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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

    // Fonction pour avoir la reservation finale
    public function getFinalBooking($token, $_booking_number){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept-Language: fr-FR"
            ],
            CURLOPT_URL => "https://api.bus.blablacar-acc.cloud.sqills.com/api/v2/booking/".$_booking_number,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
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

    
    
    
}