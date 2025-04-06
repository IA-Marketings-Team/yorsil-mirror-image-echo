<?php // src/Service/SoapService.php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SoapServiceGuzzle
{
    private $client;

    public function __construct()
    {
        // Initialiser le client Guzzle avec l'URL du WSDL
        $this->client = new Client([
            'base_uri' => 'https://194.204.248.104/unipay/services/CanalPaiementLiteral?wsdl', // Mettez l'URL de votre service SOAP ici
            'headers' => [
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction' => 'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma/getImpayes', // Mettez l'action SOAP ici
            ],
        ]);
    }

    public function xmlToArray($xmlObject) {
        $array = [];

        foreach ($xmlObject->children() as $child) {
            $key = $child->getName();
            $array[$key] = $this->xmlToArray($child);
        }

        // Si l'élément n'a pas d'enfants, retourne sa valeur
        if (empty($array)) {
            return (string)$xmlObject;
        }

        return $array;
    }


    public function getImpayes($gen,$_creance_id,$_creancier_id,$_data)
    {
        $frnVals = '<frnVals arrayType="skel:CreancierVals[]">';

        foreach ($_data as $item) {
            $frnVals .= '<CreancierVals><nomChamp>'.$item['nom_champ'].'</nomChamp><valChamp>'.$item['val_champ'].'</valChamp></CreancierVals>';
        }
        $frnVals .= '</frnVals>';

        $xml = <<<XML
        <?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:soapenc="http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma">
            <soapenv:Body>
                <soapenc:getImpayes soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">
                    <ireq xsi:type="skel:ImpayesRequest">
                        <MAC>$gen</MAC>
                        <aquereurID>823</aquereurID>
                        <canalID>2</canalID>
                        <creanceID>$_creance_id</creanceID>
                        <creancierID>$_creancier_id</creancierID>
                        <dateServeur>20190903100000</dateServeur>
                        $frnVals
                        <location></location>
                        <modeID>2</modeID>
                        <outlet>9999999999</outlet>
                        <refTxFatourati></refTxFatourati>
                        <refTxSysPmt></refTxSysPmt>
                        <typeCanal>7</typeCanal>
                    </ireq>
                </soapenc:getImpayes>
            </soapenv:Body>
        </soapenv:Envelope>
        XML;

        try {
            // Création d'un client SOAP
            $client = new \SoapClient('https://194.204.248.104/unipay/services/CanalPaiementLiteral?wsdl', [
                'trace' => 1,
                'exceptions' => true,
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                    ],
                ]),
            ]);

            // Envoi de la requête
            $response = $client->__doRequest($xml, 'https://194.204.248.104/unipay/services/CanalPaiementLiteral', 'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma/getImpayes', SOAP_1_1);

            $xmlObject = simplexml_load_string($response);

            $doms = new \DOMDocument();
            $doms->loadXML($response);
            $doms->preserveWhiteSpace = false;
            $doms->formatOutput = true;
            $formattedXml = $doms->saveXML();
            $formattedXml = str_replace("\n", "\r\n", $formattedXml);

            $xmlObject->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xmlObject->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $xmlObject->registerXPathNamespace('ns', 'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma');

            $impayesParams  = $xmlObject->xpath('//impayesParams/item');
            $montantTTC     = $xmlObject->xpath('//montantTotalTTC')[0];
            $nbreCreances   = $xmlObject->xpath('//nbreCreances')[0];
            $refTxFatourati = $xmlObject->xpath('//refTxFatourati')[0];

            return [$impayesParams,$montantTTC,$refTxFatourati];

        } catch (\SoapFault $e) {
            echo "Erreur : {$e->getMessage()}";
        }
    }

    public function setConfirmation($mac,$data,$refTxFatourati,$_creancier_id,$_creance_id,$refTxSysPmt,$paiement,$total){
        $xml=new \SimpleXMLElement(<<<XML
        <?xml version="1.0" encoding="UTF-8"?>
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:skel="http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma">
           <soapenv:Header/>
           <soapenv:Body>
              <skel:setConfirmation>
                 <in0>
                    <MAC>$mac</MAC>
                    <aquereurID>823</aquereurID>
                    <canalID>2</canalID>
                    <codeAutorisation>190799</codeAutorisation>
                    <codeDevise>504</codeDevise>
                    <codeReconciliation>00</codeReconciliation>
                    <creanceID>$_creance_id</creanceID>
                    <creancierID>$_creancier_id</creancierID>
                    <dateServeur>20201126044712</dateServeur>
                    <empreintPaiement>0000</empreintPaiement>
                    <listeArticleSelectionne>
                    </listeArticleSelectionne>
                    <location>Morocco</location>
                    <modeID>2</modeID>
                    <montantTotalTTC>$total</montantTotalTTC>
                    <outlet>1</outlet>
                    <paiementTotal>$paiement</paiementTotal>
                    <refTxFatourati>$refTxFatourati</refTxFatourati>
                    <refTxSysPmt>$refTxSysPmt</refTxSysPmt>
                    <typeCanal>7</typeCanal>
                 </in0>
                 </skel:setConfirmation>
              </soapenv:Body>
        </soapenv:Envelope>
        XML);

        $listeArticleSelectionne = $xml->xpath('//listeArticleSelectionne')[0];

        foreach ($data as $item) {
            $article = $listeArticleSelectionne->addChild('listeArticleSelectionne');
            $article->addChild('idArticle', $item['id']);
            $article->addChild('prixTTC', number_format($item['prix'], 2, '.', ''));
            $article->addChild('typeArticle', $item['type']);
        }

        // Utiliser DOMDocument pour bien formater le XML
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false; // Ne pas conserver les espaces vides
        $dom->formatOutput = true; // Activer le formatage
        $dom->loadXML($xml->asXML()); // Charger le XML dans DOMDocument
    //dd($xml->asXML());
        try {
            // Création d'un client SOAP
            $client = new \SoapClient('https://194.204.248.104/unipay/services/CanalPaiementLiteral?wsdl', [
                'trace' => 1,
                'exceptions' => true,
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                    ],
                ]),
            ]);

            // Envoi de la requête
            $response = $client->__doRequest($dom->saveXML(), 'https://194.204.248.104/unipay/services/CanalPaiementLiteral', 'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma/setConfirmation', SOAP_1_1);
            if ($response) {
                $xmlObject = simplexml_load_string($response);
                $xmlObject->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
                $xmlObject->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
                $xmlObject->registerXPathNamespace('ns', 'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma');
            }
            

            return ($response) ? $xmlObject->xpath('//setConfirmationReturn')[0] : null;
            
        } catch (\SoapFault $e) {
            echo "Erreur : {$e->getMessage()}";
        }
    }

    public function setAnnulation($mac,$data,$refTxFatourati,$_creancier_id,$_creance_id,$refTxSysPmt,$paiement,$total){
        $xml=new \SimpleXMLElement(<<<XML
        <?xml version="1.0" encoding="UTF-8"?>
            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:skel="http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma">
               <soapenv:Header/>
               <soapenv:Body>
                  <skel:setAnnulation>
                     <in0>
                      <MAC>$mac</MAC>
                        <aquereurID>823</aquereurID>
                        <canalID>2</canalID>
                        <codeAutorisation>190799</codeAutorisation>
                        <codeDevise>504</codeDevise>
                        <codeReconciliation>00</codeReconciliation>
                        <creanceID>$_creance_id</creanceID>
                        <creancierID>$_creancier_id</creancierID>
                        <dateServeur>20201126044712</dateServeur>
                        <empreintPaiement>0000</empreintPaiement>
                        <listeArticleSelectionne>
                        </listeArticleSelectionne>
                        <location>Morocco</location>
                        <modeID>2</modeID>
                        <montantTotalTTC>$total</montantTotalTTC>
                        <outlet>1</outlet>
                        <paiementTotal>$paiement</paiementTotal>
                        <refTxFatourati>$refTxFatourati</refTxFatourati>
                        <refTxSysPmt>$refTxSysPmt</refTxSysPmt>
                        <typeCanal>7</typeCanal>
                     </in0>
                  </skel:setAnnulation>
               </soapenv:Body>
            </soapenv:Envelope>
        XML);

        $listeArticleSelectionne = $xml->xpath('//listeArticleSelectionne')[0];

        for($_i = 0; $_i < count($data) ; $_i++) { 
            $article = $listeArticleSelectionne->addChild('listeArticleSelectionne');
            $article->addChild('idArticle', $data[$_i]['id']);
            $article->addChild('prixTTC', number_format($data[$_i]['prix'], 2, '.', ''));
            $article->addChild('typeArticle', $data[$_i]['type']);
        }

        // Utiliser DOMDocument pour bien formater le XML
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false; // Ne pas conserver les espaces vides
        $dom->formatOutput = true; // Activer le formatage
        $dom->loadXML($xml->asXML()); // Charger le XML dans DOMDocument

        try {
            // Création d'un client SOAP
            $client = new \SoapClient('https://194.204.248.104/unipay/services/CanalPaiementLiteral?wsdl', [
                'trace' => 1,
                'exceptions' => true,
                'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                    ],
                ]),
            ]);

            // Envoi de la requête
            $response = $client->__doRequest($dom->saveXML(), 'https://194.204.248.104/unipay/services/CanalPaiementLiteral', 'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma/setAnnulation', SOAP_1_1);

            $xmlObject = simplexml_load_string($response);

            $xmlObject->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xmlObject->registerXPathNamespace('xsi', 'http://www.w3.org/2001/XMLSchema-instance');
            $xmlObject->registerXPathNamespace('ns', 'http://skeleton.services.canalpaiement.unipay.maroctelecommerce.ma');

            return $xmlObject->xpath('//setAnnulationReturn')[0];
            
        } catch (\SoapFault $e) {
            echo "Erreur : {$e->getMessage()}";
        }
 
    }
}
