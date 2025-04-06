<?php

namespace App\Service\Api;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceFerry
{
    private $_entity_manager;
    private $_container;
    private $_email = 'abdelhak@yorsil.com';
    private $_password = 'mai2024';
    
    public function __construct(
        EntityManagerInterface $_entity_manager,
        ContainerInterface $_container
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }

    // Fonction pour avoir l'access token de l'API
    public function getAccessToken(){
        $curl = curl_init();

        $data = array(
            "email" => $this->_email ,
            "password" => $this->_password 
        );
         
        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://apidev.ferry4you.com/api/v1/agence/login",
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
        return [$response,$is_error];
    }

    // Fonction qui retourne les routes des ferries
    public function getRoute($token){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://apidev.ferry4you.com/api/v1/route/fr",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
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

    // Fonction qui retourne les Traversées des ferries (Retour facultatif)
    public function postSailings($token, $data){
        $curl = curl_init();

        $datatest = array(
            "CodeRoute" => "TG",
            "DateReturn" => "2025-02-27",
            "DateDeparture" => "2025-02-24",
            "Dog" => 1,
            "Cat" => 1,
            "Height" => 0,
            "Length" => 0,
            "Child" => 1,
            "Adult" => 1,
            "AgeChild1" => 15,
            "AgeChild2" => "",
            "AgeChild3" => "",
            "AgeChild4" => "",
            "AgeChild5" => "",
            "transport" => "Bicycle",
            "Trailer1" => "",
            "TrailerH" => "",
            "DepartureName" => "TUNIS",
            "DestinationName" => "GENOVA",
            "language" => "fr"
        );

        // Convertir les données en chaîne de requête URL-encoded
        $postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://apidev.ferry4you.com/api/v1/traverse/xml/GetSailings",
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
        return [$response,$is_error];
    }

    // Fonction qui retourne les services à bord du Ferry
    public function postServices($token){
        
        $curl = curl_init();

        $data = [
            "PortDeparture"=> "TNTUN",
            "PortArrival"=> "ITGOA",
            "DateDeparture"=> "03-03-2025",
            "DateReturn"=> "06-03-2025",
            "DateDepartureTime"=> "10:00",
            "DateDepartureTimeReturn"=> "16:00",
            "Dog"=> 0,
            "Cat"=> 0,
            "FareType"=> "FCWTS",
            "Height"=> 0.174,
            "Length"=> 0.405,
            "Child"=> 0,
            "Adult"=> 1,
            "AgeChild1"=> "0",
            "AgeChild2"=> "0",
            "AgeChild3"=> "0",
            "AgeChild4"=> "0",
            "AgeChild5"=> "0",
            "transport"=> "Car",
            "Trailer1"=> "0",
            "code0"=> "",
            "session"=> "",
            "company"=> "ctn",
            "token"=> "TokenGrimaldi",
            "language"=> "fr"
        ];

        // Convertir les données en chaîne de requête URL-encoded
        //$postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://apidev.ferry4you.com/api/v1/traverse/xml/service",
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
        return [$response,$is_error];
    }

    // Fonction pour creer une reservation de Ferry (Sailings et services inclus dedans)
    public function creerBookingFerry($token){

        $curl = curl_init();

        $data = [
            "company"=> "-ctn",
            "reservationPriceTotal"=> 100,
            "prAvailable"=> true,
            "email"=> "testPasserelle@gmail.com",
            "phoneNumber"=> 222211111,
            "codePhone"=> "Tunisia-(TM/TUN)+216",
            "createdBy"=> 3,
            "updatedBy"=> null,
            "vehicleLength"=> 4,
            "vehicleHeight"=> 4,
            "vehicleType"=> "Car",
            "traversecityDepartment"=> "TUNIS",
            "traversecityArrival"=> "GENOVA",
            "traversebateDepartment"=> "27/12/2024",
            "traversebateDepartmentureReturn"=> "28/12/2024",
            "traversebateArrival"=> "28/12/2024",
            "traversebateArrivalReturn"=> "29/12/2024",
            "DateDepartmentTime"=> "10:00",
            "TypeCar"=> "ICE",
            "DateDepartmentTimeReturn"=> "16:00",
            "DateArrivalTime"=> "10:00",
            "DateArrivalTimeReturn"=> "16:00",
            "traversehourDepartmenture"=> "10:00",
            "traversehourArrival"=> "16:00",
            "traversehourDepartmentureReturn"=> "16:00",
            "traversehourArrivalReturn"=> "16:00",
            "traversePortDeparture"=> "TNTUN",
            "traversePortArrival"=> "ITGOA",
            "traverseDurationRoute"=> "24:00",
            "traverseDurationRouteReturn"=> "24:00",
            "traverseCodego"=> "8",
            "traverseCoderreturn"=> "16",
            "registeredVehicle"=> "m1",
            "vehicleDescription"=> "desc",
            "vehiclePtSupply"=> false,
            "vehicleISCamper"=> false,
            "Brand"=> "AC",
            "Model"=> "AC-Ace-Bristol",
            "vehicleHasTrailer"=> false,
            "fareType"=> "FGWTS",
            "typeInstallationCode"=> "FAUT",
            "typeInstallationCapacity"=> 1,
            "typeInstallationDescription"=> "Place-assise-en-salonfauteuil-port-Avant(Faut)",
            "typeInstallationReturnCode"=> "FAUT",
            "typeInstallationReturnCapacity"=> 1,
            "typeInstallationReturnDescription"=> "Place-assise-en-salonfauteuil-port-Avant(Faut)",
            "typeInstallationPrice"=> 0,
            "typeInstallationName"=> "",
            "passenger"=> [],
            "passenger18"=> [],
            "passenger25"=> [
                "0"=> [
                    "gender"=> "Mr",
                    "firstName"=> "amine",
                    "lastName"=> "mohamed",
                    "birth"=> "1991-11-11",
                    "country"=> "Algeria (DZ/DZA)",
                    "passport"=> "2113123",
                    "expiry"=> "2025-03-19",
                ]     
            ],
            "child"=> [
                "0" => [
                    "gender"=> "Mr",
                    "country"=> "Algeria (DZ/DZA)",
                    "firstName"=> "test",
                    "lastName"=> "test",
                    "passport"=> "32232323",
                    "birth"=> "2008-11-11",
                    "expiry"=> "2025-03-19",
                ]
            ],
            "mealGo"=> "",
            "mealReturn"=> "",
            "nbDog"=> "",
            "nbPets"=> "",
            "token"=> "TokenGrimaldi"
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://apidev.ferry4you.com/api/v1/reservation/create/book",
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
        return [$response,$is_error];
    }

    // Fonction pour confirmer la reservation de Ferry
    public function confirmerBookingFerry($token){
        $curl = curl_init();

        $data = [
            "codeReservation" => "484d26be34ee5439",
            "session" => "",
            "company" => "ctn",
            "email" => "testPasserelle@gmail.com",
            "token" => ""
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://apidev.ferry4you.com/api/v1/reservation/confirm/book",
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
        return [$response,$is_error];
    }

    // Fonction pour avoir le details de la reservation de Ferry
    public function detailsBookingFerry($token){
        $curl = curl_init();

        $data = [
            "reference" => "106408"
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://apidev.ferry4you.com/api/v1/traverse/xml/DetailsBook",
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
        return [$response,$is_error];
    }

    // Fonction pour avoir les frais d'annulation de la reservation
    public function getCancelCharge($token){
        
        $curl = curl_init();

        $data = [
            "company" => "ctn",
            "reference" => "106408"
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://apidev.ferry4you.com/api/v1/reservation/GetCancelCharge",
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
        return [$response,$is_error];
    }

    // Fonction pour annuler une reservation 
    public function annulerBooking($token){
        $curl = curl_init();

        $data = [
            "company" => "ctn",
            "reference" => "106408"
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://apidev.ferry4you.com/api/v1/reservation/CancelBooking",
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
        return [$response,$is_error];
    }

}