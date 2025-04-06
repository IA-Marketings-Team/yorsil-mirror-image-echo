<?php

namespace App\Service\Api;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceDing
{
    
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
            "client_id" => "57aa1f85-1846-4436-9a1d-5add72619030",
            "client_secret" => "81nRrN5hnNsJXK53mZLc4O4qfgDaaUQWY9G3W8qXrKg=",
            "grant_type" => "client_credentials"
        );
         
        // Convertir les données en chaîne de requête URL-encoded
		$postFields = http_build_query($data);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . strlen($postFields)
            ],
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_URL => "https://idp.ding.com/connect/token",
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

	// Fonction qui retourne tous les operateurs par pays
	public function getProviders($token,$_country_isos,$_region_codes,$accountNumber){

		$curl = curl_init();

		curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://api.dingconnect.com/api/V1/GetProviders?providerCodes=&countryIsos=".$_country_isos."&regionCodes=".$_region_codes."&accountNumber=".$accountNumber,
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
        return [$response,$is_error];
	}

	// Fonction qui retourne le status de l'operateur selectionné s'il est apte pour un transfert ou pas
	public function getProvidersStatus($token,$_code){

		$curl = curl_init();

		curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://api.dingconnect.com/api/V1/GetProviderStatus?providerCodes=".$_code,
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
        return [$response,$is_error];
		
	}
	
	// Fonction qui retourne tous les offres d'un par pays à l'aide du provider_code
	public function getProducts($token,$_country_isos,$_provider_code,$_region_codes){
		$curl = curl_init();

		curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://api.dingconnect.com/api/V1/GetProducts?countryIsos=".$_country_isos."&providerCodes=".$_provider_code."&skuCodes=&benefits=&regionCodes=".$_region_codes."&accountNumber=",
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
        return [$response,$is_error];
		
	}

	// Fonction pour faire une transfert a l'aide du code de produit
	public function sendTransfert($token,$_numero,$_sku_code,$_send_value){
		$curl = curl_init();

        $data = array(
            "SkuCode"=> $_sku_code,
		    "SendValue" => $_send_value,
		    "AccountNumber" => $_numero,
		    "DistributorRef" => "123",
		    "ValidateOnly" => false
        );

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://api.dingconnect.com/api/V1/SendTransfer",
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

    // Fonction qui retourne un transfert a l'aide du transfert ref, distributor ref, account number
    public function listeTransfertRecord($token,$_transfert_ref,$_distributor_ref,$_account_number){
        
        $curl = curl_init();

        $data = array(
            "TransferRef" => $_transfert_ref,
            "DistributorRef" => $_distributor_ref,
            "AccountNumber" => $_account_number,
            "Skip" => 0,
            "Take" => 1
        );

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $token
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_URL => "https://api.dingconnect.com//api/V1/ListTransferRecords",
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



    // Fonction qui retourne la description d'un produit a l'aide de son skucode
    public function getProductDescriptions($token,$_langage_code,$_skuCodes){
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://api.dingconnect.com/api/V1/GetProductDescriptions?languageCodes=".$_langage_code."&skuCodes=".$_skuCodes,
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
        return [$response,$is_error];
        
    }

    // Fonction qui retourne le solde de Ding
    public function getBalance($token){
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token
            ],
            CURLOPT_URL => "https://api.dingconnect.com/api/V1/GetBalance",
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
        return [$response,$is_error];
        
    }

}