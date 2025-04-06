<?php

namespace App\Service\Metier;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceMetierTransfert
{
    
    public function __construct(
        EntityManagerInterface $_entity_manager,
        ContainerInterface $_container
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }

    private $lastToken = null;
    private $tokenExpiry = null;

    public function getAccessToken(){
        // Vérifier si le token est encore valide
        if ($this->lastToken && $this->tokenExpiry > new \DateTime()) {
            error_log("Using cached access token");
            return [$this->lastToken, null, null];
        }

        error_log("Requesting new access token from Reloadly");
        
        $curl = curl_init();

        $payload = [
            "client_id" => $this->_container->getParameter('reloadly_client_id'),
            "client_secret" => $this->_container->getParameter('reloadly_client_secret'),
            "grant_type" => "client_credentials",
            "audience" => "https://topups.reloadly.com"
        ];
          
        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Accept: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_URL => "https://auth.reloadly.com/oauth/token",
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
        ]);
        
        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        curl_close($curl);

        if ($error) {
            error_log("Reloadly auth connection error");
            return [$response, $error, 'CONNECTION_ERROR'];
        }

        if ($httpCode !== 200) {
            $errorMsg = "Reloadly auth returned HTTP $httpCode";
            if ($response) {
                $apiResponse = json_decode($response);
                $errorMsg .= " - " . ($apiResponse->error ?? $apiResponse->message ?? 'Unknown error');
            }
            error_log($errorMsg);
            return [$response, $errorMsg, 'AUTH_ERROR'];
        }

        $result = json_decode($response);
        if (!$result) {
            $errorMsg = "Invalid JSON response from Reloadly auth";
            error_log($errorMsg);
            return [$response, $errorMsg, 'INVALID_RESPONSE'];
        }

        if (empty($result->access_token)) {
            $errorMsg = "No access token in response";
            error_log($errorMsg);
            return [$response, $errorMsg, 'NO_TOKEN'];
        }

        error_log("Successfully obtained access token");
        return [$response, null, null];
	}

	public function getOperators($token, $phone, $countryisocode){
        error_log("Getting operators for phone: $phone, country: $countryisocode");

        $query = array(
            "suggestedAmountsMap" => "true",
            "suggestedAmounts" => "false"
        );

        $curl = curl_init();
        $url = "https://topups.reloadly.com/operators/auto-detect/phone/" . $phone . "/countries/" . $countryisocode . "?" . http_build_query($query);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept: application/com.reloadly.topups-v1+json"
            ],
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            error_log("Reloadly API connection error: " . $error);
            return [$response, $error, 'CONNECTION_ERROR'];
        }

        if ($httpCode !== 200) {
            $errorMsg = "Reloadly API returned HTTP $httpCode";
            if ($response) {
                $apiResponse = json_decode($response);
                $errorMsg .= " - " . ($apiResponse->message ?? $apiResponse->error ?? 'Unknown error');
            }
            error_log($errorMsg);
            return [$response, $errorMsg, 'API_ERROR'];
        }

        $result = json_decode($response);
        if (!$result) {
            $errorMsg = "Invalid JSON response from Reloadly API";
            error_log($errorMsg);
            return [$response, $errorMsg, 'INVALID_RESPONSE'];
        }

        if (empty($result->operators)) {
            error_log("No operators found for country: $countryisocode");
        } else {
            error_log("Found " . count($result->operators) . " operators");
        }

        return [$response, null, null];
    }

	public function getBalance($token){
        error_log("Requesting account balance from Reloadly");

		$curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $token,
                "Accept: application/com.reloadly.topups-v1+json"
            ],
            CURLOPT_URL => "https://topups.reloadly.com/accounts/balance",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            error_log("Reloadly balance API connection error: " . $error);
            return [$response, $error, 'CONNECTION_ERROR'];
        }

        if ($httpCode !== 200) {
            $errorMsg = "Reloadly balance API returned HTTP $httpCode";
            if ($response) {
                $apiResponse = json_decode($response);
                $errorMsg .= " - " . ($apiResponse->message ?? $apiResponse->error ?? 'Unknown error');
            }
            error_log($errorMsg);
            return [$response, $errorMsg, 'API_ERROR'];
        }

        $result = json_decode($response);
        if (!$result) {
            $errorMsg = "Invalid JSON response from Reloadly balance API";
            error_log($errorMsg);
            return [$response, $errorMsg, 'INVALID_RESPONSE'];
        }

        if (!isset($result->balance)) {
            $errorMsg = "No balance in response";
            error_log($errorMsg);
            return [$response, $errorMsg, 'NO_BALANCE'];
        }

        error_log("Successfully retrieved balance: " . $result->balance);
        return [$response, null, null];
	}

	public function getDevisePays($base_price,$code){

		$req_url = 'https://v6.exchangerate-api.com/v6/d31ec0240ed572b9c5b570b7/latest/MGA';
		$response_json = file_get_contents($req_url);
		$result = 0;

		// Continuing if we got a result
		if(false !== $response_json) {

		    // Try/catch for json_decode operation
		    try {

				// Decoding
				$response = json_decode($response_json);

				// Check for success
				if('success' === $response->result) {
					// YOUR APPLICATION CODE HERE, e.g.
					$result = round(($base_price * $response->conversion_rates->$code), 2);

				}

		    }
		    catch(Exception $e) {
		        // Handle JSON parse error...
		    }
		}

		return $result;

	}

    private $lastValidationError = null;

    public function getValidationError() {
        return $this->lastValidationError;
    }

    public function isValidNumber($number){
        $token = $this->_container->getParameter('number_validation_api_key');
        $this->lastValidationError = null;

        error_log("Validating number: " . $number);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => array(
                "Content-Type: text/plain",
                "apikey: ". $token
            ),
            CURLOPT_URL => "https://api.apilayer.com/number_verification/validate?number=".$number,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            error_log("Number validation API error: " . $error);
            $this->lastValidationError = "Erreur de connexion au service de validation";
            return false;
        }

        if ($httpCode !== 200) {
            error_log("Number validation API returned HTTP $httpCode");
            $this->lastValidationError = "Service de validation indisponible";
            return false;
        }

        $result = json_decode($response);

        if (!$result) {
            error_log("Invalid JSON response from validation API");
            $this->lastValidationError = "Réponse invalide du service de validation";
            return false;
        }

        if (isset($result->error)) {
            error_log("Validation API error: " . json_encode($result->error));
            $this->lastValidationError = $result->error->message ?? "Erreur de validation";
            return false;
        }

        if (!isset($result->valid)) {
            error_log("Missing 'valid' field in validation response");
            $this->lastValidationError = "Format de réponse invalide";
            return false;
        }

        if (!$result->valid) {
            $this->lastValidationError = "Numéro invalide - " . ($result->error ?? "Format incorrect");
            error_log("Number validation failed: " . $this->lastValidationError);
        }

        return $result->valid;
    }

    public function listeTransfert($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "trans.id DESC";
        $_trans = EntityName::IP_TRANSFERT;

        $_dql = "SELECT DATE_FORMAT(trans.date, '%d-%m-%Y') as transDate,
                        bout.nom,
                        trans.numero,
                        trans.montant,
                        CASE 
                            WHEN trans.commission IS NOT NULL THEN CONCAT(trans.commission, '%')
                            ELSE '--' END as commission,
                        CASE 
                            WHEN trans.fx IS NOT NULL THEN JSON_UNQUOTE(JSON_EXTRACT(trans.fx, '$[0]'))
                            ELSE '--' END as fx,
                        trans.operateur,
                        trans.pays,
                        trans.type,
                        trans.status,
                        trans.id
                 FROM $_trans trans
                 LEFT JOIN trans.bout as bout 
                 WHERE (bout.nom LIKE :search
                    OR  trans.montant LIKE :search
                    OR  trans.numero LIKE :search 
                    OR  trans.operateur LIKE :search
                    OR  trans.type LIKE :search
                    OR  trans.pays LIKE :search
                    OR DATE_FORMAT(trans.date, '%d-%m-%Y') LIKE :search
                 )
                 ORDER BY $_order_by";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
            
        return [$_query->getResult(), $this->countTransfert($_search)];
    }

    public function countTransfert($_search){
        $_trans = EntityName::IP_TRANSFERT;

        $_dql = "SELECT COUNT(trans.id) AS nbTotal
                 FROM $_trans trans
                 LEFT JOIN trans.bout as bout
                 WHERE (bout.nom LIKE :search
                    OR  trans.montant LIKE :search
                    OR  trans.numero LIKE :search 
                    OR  trans.operateur LIKE :search
                    OR  trans.type LIKE :search
                    OR  trans.pays LIKE :search
                    OR DATE_FORMAT(trans.date, '%d-%m-%Y') LIKE :search
                 )";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }

    public function listeTransaction($_page, $_nb_max_page, $_search, $_order_by){
        $_order_by = $_order_by ? $_order_by : "trans.created_at DESC";
        $_trans    = EntityName::IP_TRANSACTION;
        $_boutique = EntityName::IP_BOUTIQUE;

        $_dql = "SELECT DATE_FORMAT(trans.created_at, '%d-%m-%Y') as transDate,
                        trans.id,
                        (SELECT bout.nom FROM $_boutique bout WHERE bout.id = JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.boutique')) ) as nomb,
                        (SELECT user.nom FROM $_boutique bouts LEFT JOIN bouts.user user  WHERE bouts.id = JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.boutique')) ) as gerant,
                        CASE 
                            WHEN JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.prefix')) IS NOT NULL THEN CONCAT(JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.prefix')),trans.destination_number) 
                            ELSE trans.destination_number END as numero,
                        trans.amount,
                        JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.pays')) as pays,
                        trans.trx_status
                 FROM $_trans trans
                 WHERE (trans.amount LIKE :search
                    OR  trans.destination_number LIKE :search
                    OR  trans.id LIKE :search
                    OR DATE_FORMAT(trans.created_at, '%d-%m-%Y') LIKE :search
                    OR JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.pays')) LIKE :search
                    OR CONCAT(JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.prefix')),trans.destination_number) LIKE :search
                 )
                 ORDER BY $_order_by";
        // -- OR (SELECT boutss.nom FROM $_boutique boutss WHERE boutss.id = JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('datass',trans.custom_data),'$.datass')),'$.boutique'))) LIKE :search
        // CONCAT(JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.prefix')),trans.destination_number) as numero

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%")
            ->setFirstResult($_page)
            ->setMaxResults($_nb_max_page);
            
        return [$_query->getResult(), $this->countTransaction($_search)];
    }

    public function countTransaction($_search){
        $_trans = EntityName::IP_TRANSACTION;

        $_dql = "SELECT COUNT(trans.id) AS nbTotal
                 FROM $_trans trans
                 WHERE (trans.amount LIKE :search
                    OR  trans.destination_number LIKE :search
                    OR  trans.id LIKE :search
                    OR DATE_FORMAT(trans.created_at, '%d-%m-%Y') LIKE :search
                    OR JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.pays')) LIKE :search
                    OR CONCAT(JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(JSON_EXTRACT(JSON_OBJECT('data',trans.custom_data),'$.data')),'$.prefix')),trans.destination_number) LIKE :search
                 )";

        $_query = $this->_container->get('doctrine.orm.entity_manager')->createQuery($_dql);
        $_query->setParameter('search', "%$_search%");

        return $_query->getOneOrNullResult()['nbTotal'];
    }
}
