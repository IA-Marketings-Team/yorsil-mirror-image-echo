<?php

namespace App\Service\Api;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ServiceSog
{
    
    public function __construct(
        EntityManagerInterface $_entity_manager,
        ContainerInterface $_container,
        ParameterBagInterface $params
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
        $this->params          = $params;
    }

    // Fonction pour avoir l'access token de l'API
    // TODO: supprimer cela.
	public function postAuth(){
		$curl = curl_init();
        
        $test = ["value"=> "My testing value"];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Basic ". base64_encode('67615767' . ":" . 'testpassword_07xupai9XjgIzA5pJzb4jiv9kmfowCfHnq6ZghoK60fe6')
            ],
            CURLOPT_POSTFIELDS => json_encode($test),
            CURLOPT_URL => "https://api-sogecommerce.societegenerale.eu/api-payment/V4/Charge/SDKTest",
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

    public function createFormToken($amount,$order,$email){
        $curl  = curl_init();
        
        $total = ((float)$amount) * 100;

        $test = [
            "amount"=>  $total,
            "currency"=> "EUR",
            "orderId"=>  $order,
            "customer"=> [
                "email"=> $email
            ]
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Basic ". base64_encode('67615767' . ":" . 'prodpassword_UDcmgIlcQJr8nsOPy2c38LEZkmm4kgvlNR8hj99D8wS0j')
            ],
            CURLOPT_POSTFIELDS => json_encode($test),
            CURLOPT_URL => "https://api-sogecommerce.societegenerale.eu/api-payment/V4/Charge/CreatePayment",
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
