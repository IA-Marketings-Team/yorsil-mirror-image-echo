<?php

namespace App\Service\Api;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceReloadly
{
    private $_entity_manager;
    private $_container;
    
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

        // url teste 
        //"client_id" => "zc66wb2Wl3RsJBNKrmCviWe5YrbLDbjo",
        //"client_secret" => "V3CHiViopT-tDpFjwkqLYB3lzs1t7P-uLj6an0jrXS1d0icx9JoixUTkwxDY2Y1",
        
        // url prod
        //"client_id" => "gMixLVzTlP54Zsn2e3BvUNCC27s0Rjrx", 
        //"client_secret" => "3I13IV48Gx-VpafAFgkPqBUghN0kmM-seN1kQazIBPvv9IfatjcoOBZoWnR9LlN",  

        $payload = array(
            "client_id" => "gMixLVzTlP54Zsn2e3BvUNCC27s0Rjrx",
            "client_secret" => "3I13IV48Gx-VpafAFgkPqBUghN0kmM-seN1kQazIBPvv9IfatjcoOBZoWnR9LlN",
            "grant_type" => "client_credentials",
            "audience" => "https://topups.reloadly.com"
        );
          
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_URL => "https://auth.reloadly.com/oauth/token",
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

    // Fonction qui retourne les infos(solde,devise,nom de l'argent) de l'utilisateur
    public function getAccountBalance($token){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://topups.reloadly.com/accounts/balance",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            return $error;
        }

        return $response;
    }

    // Fonction qui retourne les operateurs par pays
    public function getOperatorByIso($token,$code){

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://topups.reloadly.com/operators/countries/".$code,
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
        return [$response,$is_error];
    }

    // Fonction qui retourne les montants suggérés ou min-max par operateur
    public function getOperatorAutoDetect($token,$phone,$countryisocode){

        $query = array(
        "suggestedAmountsMap" => "true",
        "suggestedAmounts" => "false"
        );

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://topups.reloadly.com/operators/auto-detect/phone/" . $phone . "/countries/" . $countryisocode . "?" . http_build_query($query),
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
        return [$response,$is_error];
    }

    // Fonction pour effectuer une recharge
    public function postTopUp($token, $payload)
    {
        //customIdentifier unique value
        
        $curl = curl_init();

        $jsonPayload = json_encode($payload);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token
            ],
            CURLOPT_POSTFIELDS => $jsonPayload,
            CURLOPT_URL => "https://topups.reloadly.com/topups",
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

    // Fonction qui retourne l'état en temps réel d'une recharge en faisant une demande avec son Id
    public function getTopUpStatus($token,$id)
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://topups.reloadly.com/topups/".$id."/status",
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

        return [$response,$is_error];
    }
}