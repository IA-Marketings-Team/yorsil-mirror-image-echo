<?php // src/Service/SoapClientService.php
namespace App\Service;

use SoapClient;
use SoapHeader;

class SoapClientService
{
    private $client;
    private $client_utf;

    public function __construct()
    {
        $wsdl = 'https://194.204.248.104/unipay/services/CanalPaiementLiteral?wsdl'; 
        $this->client = new SoapClient($wsdl, [
            'trace' => true, // pour le débogage
            'exceptions' => true,
            'soap_version' => SOAP_1_1,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'stream_context' => stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]),
        ]);
    }

    public function getForms(array $params)
    {
        // $header = new SoapHeader(
        //     'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma',
        //     'getListCreanciersRequest', 
        //     null
        // );
        // $this->client->__setSoapHeaders($header);
        try {
            $response = $this->client->__soapCall('getForms', $params);

            return $response;
        } catch (\SoapFault $fault) {
            throw new \Exception("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})");
        }
    }

    public function getListCreances(array $params)
    {
        $header = new SoapHeader(
            'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma',
            'getListCreancesRequest', 
            null
        );
        $this->client->__setSoapHeaders($header);
        try {
            $response = $this->client->__soapCall('getListCreances', $params);
             // Affichez la requête SOAP envoyée et la réponse reçue pour débogage
            // echo "Request : " . htmlspecialchars($this->client->__getLastRequest()) . "\n";
            // echo "Response : " . htmlspecialchars($this->client->__getLastResponse()) . "\n";

            return $response;
        } catch (\SoapFault $fault) {
            throw new \Exception("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})");
        }
    }

    public function getListCreanciers(array $params)
    {
        $header = new SoapHeader(
            'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma',
            'getListCreanciersRequest', 
            null
        );
        $this->client->__setSoapHeaders($header);
        try {
            $response = $this->client->__soapCall('getListCreanciers', $params);

            return $response;
        } catch (\SoapFault $fault) {
            throw new \Exception("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})");
        }
    }

    // public function getImpayes(array $params)
    // {
    //     $header = new SoapHeader(
    //         'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma',
    //         'getImpayesRequest', 
    //         null
    //     );
    //     $this->client->__setSoapHeaders($header);
    //     try {
    //         $response = $this->client->__soapCall('getImpayes', $params);
    //         echo "Request : " . htmlspecialchars($this->client->__getLastRequest()) . "\n";
    //         echo "Response : " . htmlspecialchars($this->client->__getLastResponse()) . "\n";

    //         return $response;
    //     } catch (\SoapFault $fault) {
    //         throw new \Exception("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})");
    //     }
    // }

     public function getImpayes()
    {
     $header = new SoapHeader(
         'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma',
        'getImpayes',
        null 
    );
    $this->client->__setSoapHeaders($header);

    // Construction des paramètres selon la structure attendue
    $params = [
        'ireq' => [
            'MAC' => 'qVIEiuYNJ0tYB4coNYF5YQ==',
            'aquereurID' => '823',
            'canalID' => '2',
            'creanceID' => '01',
            'creancierID' => '1008',
            'dateServeur' => '20190903100000',
            'frnVals' => [
                'CreancierVals' => [
                    [
                        'nomChamp' => 'AMTAN_CLI_NUM_CONTRAT',
                        'valChamp' => '0000424'
                    ]
                ]
            ],
            'location' => '',
            'modeID' => '2',
            'outlet' => '9999999999',
            'refTxFatourati' => '',
            'refTxSysPmt' => '',
            'typeCanal' => '7'
        ]
    ];

    $headers = [
        new SoapHeader('http://schemas.xmlsoap.org/soap/envelope/', 'encodingStyle', 'http://schemas.xmlsoap.org/soap/encoding/'),
        new SoapHeader('http://www.w3.org/2001/XMLSchema-instance', 'xsi', null),
        new SoapHeader('http://www.w3.org/2001/XMLSchema', 'xsd', null)
    ];

    // Appel de la méthode getImpayes avec les en-têtes
    try{
        $response = $this->client->__soapCall('getImpayes', [$params], null, $headers);

         echo "Request : " . htmlspecialchars($this->client->__getLastRequest()) . "\n";

        dd($response);
         return $response; 
     }catch (SoapFault $fault) {
         echo "SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})\n";
    }
}

   

}
