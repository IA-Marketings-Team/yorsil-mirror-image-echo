<?php

namespace App\Service\Api;

use App\Service\Utils\EntityName;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class ServiceAleda
{
    private $_entity_manager;
    private $_container;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ContainerInterface $_container
    ) {
        $this->_entity_manager = $_entity_manager;
        $this->_container      = $_container;
    }

    // Fonction qui formatte les donnees en une x-auth
    public function getAuth($secretKey)
    {
        // Définir les parties statiques
        $agentRef    = '131041';
        $version        = '1.0';
        // prod
        $terminalRef = 'SERD47BAE5F98E94F6EB';
        // test
        //$terminalRef = 'SER79CAAE3BC53C4193B';

        // Obtenir la date actuelle en UTC
        $now = new \DateTime("now", new \DateTimeZone("UTC"));

        // Formater chaque partie de la date
        $year = $now->format('Y');
        $month = $now->format('m');
        $day = $now->format('d');
        $hours = $now->format('H');
        $minutes = $now->format('i');
        $seconds = $now->format('s');
        $milliseconds = $now->format('v'); // Les millisecondes

        // Combiner les parties pour obtenir le format désiré
        $formattedDate = $year . $month . $day . $hours . $minutes . $seconds . $milliseconds;

        // Créer la chaîne de base pour la signature
        $baseString = $agentRef . '|' . $formattedDate . '|' . $version . '|' . $terminalRef;

        $signature = hash_hmac('sha256', $baseString, $secretKey);

        // Combiner toutes les parties dans l'en-tête X-AUTH
        $xAuthHeader = $agentRef . '|' . $formattedDate . '|' . $version . '|' . $terminalRef . '|' . $signature;

        return $xAuthHeader;
    }

    // Fonction qui retourne l'access token pour etre un secretkey pour le x-auth globale
    public function getAccessToken($_x_auth)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "X-AUTH:" . $_x_auth
            ],
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/auth/token",
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/auth/token",
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

    // Fonction qui retourne si le x-auth globale est OK
    public function getXAuth($_x_auth)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "X-AUTH:" . $_x_auth
            ],
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/auth/hello?name=yorsil",
            // test
            // CURLOPT_URL => "https://iapi-server.aleda.fr/auth/hello?name=yorsil",
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

    // Fonction qui retourne le solde de l'agent
    public function getBalance($_x_auth, $_agent_ref)
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "X-AUTH:" . $_x_auth
            ],
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/demat/api/v1/agents/" . $_agent_ref . "/incur",
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/demat/api/v1/agents/".$_agent_ref."/incur",
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

    // Fonction qui retourne toute les catalogues de produits
    public function getCalogue($_x_auth, $_agent_ref)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "X-AUTH:" . $_x_auth
            ],
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/demat/api/v1/agents/" . $_agent_ref . "/catalog",
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/demat/api/v1/agents/".$_agent_ref."/catalog",
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

    // Fonction qui retourne les differents types de catalogues ainsi que les produits qui y sont 
    public function getCatalogueTree($_x_auth, $_agent_ref)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "X-AUTH:" . $_x_auth
            ],
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/demat/api/v1/agents/" . $_agent_ref . "/catalog?projection=NAVIGATE",
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/demat/api/v1/agents/".$_agent_ref."/catalog?projection=NAVIGATE",
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

    // Fonction pour ajouter une vente avec bon d'achat
    public function postSalesWithVoucher($_x_auth, $_agent_ref, $_product_code, $_qte, $_sale_ref, $_paiement, $customr_id)
    {
        $curl = curl_init();

        // Ajouter ces variables si les produits contiennent le variableAmount et CustomData
        //   "variableAmount" : null,
        //   "customerData" : {
        //     "PAN" : null
        //   },

        $sales = [
            "productCode" => $_product_code,
            "qty" => $_qte,
            "saleRef" => $_sale_ref,
            "paymentMode" => $_paiement,
            "voucherOptions" => [
                "output" => "NONE"
            ],
            "customerId" => $customr_id
        ];


        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-AUTH:" . $_x_auth
            ],
            CURLOPT_POSTFIELDS => json_encode($sales),
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/demat/api/v1/agents/" . $_agent_ref . "/sales",
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/demat/api/v1/agents/".$_agent_ref."/sales",
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

    // Fonction pour ajouter une vente avec bon d'achat avec pdf
    public function postSalesWithVoucherPdf($_x_auth, $_agent_ref, $_product_code, $_qte, $_sale_ref, $_paiement, $customer_id)
    {
        $curl = curl_init();

        // Ajouter ces variables si les produits contiennent le variableAmount et CustomData
        //   "variableAmount" : null,
        //   "customerData" : {
        //     "PAN" : null
        //   },

        $sales = [
            "productCode" => $_product_code,
            "qty" => $_qte,
            "saleRef" => $_sale_ref,
            "paymentMode" => $_paiement,
            "voucherOptions" => [
                "output" => "PDF",
                "pdfFormat" => "WIDTH80",
                "withPdfCounterMark" => true
            ],
            "customerId" => $customer_id
        ];


        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-AUTH:" . $_x_auth
            ],
            CURLOPT_POSTFIELDS => json_encode($sales),
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/demat/api/v1/agents/" . $_agent_ref . "/sales",
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/demat/api/v1/agents/".$_agent_ref."/sales",
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

    // Fonction pour reserver une vente avant de la confirmer
    public function bookingSale($_x_auth, $_agent_ref, $_product_code, $_qte, $_sale_ref, $_paiement)
    {
        $curl = curl_init();

        $sales = [
            "productCode" => $_product_code,
            "qty" => $_qte,
            "saleRef" => $_sale_ref,
            "paymentMode" => $_paiement,
            "voucherOptions" => [
                "output" => "PDF",
                "pdfFormat" => "WIDTH80",
                "withPdfCounterMark" => true
            ]
        ];


        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-AUTH:" . $_x_auth
            ],
            CURLOPT_POSTFIELDS => json_encode($sales),
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/demat/api/v1/agents/" . $_agent_ref . "/sales?booking=true",
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/demat/api/v1/agents/".$_agent_ref."/sales?booking=true",
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

    // Fonction pour confirmer une vente 
    public function confirmSale($_x_auth, $_agent_ref, $_sale_ref, $_paiement)
    {
        $curl = curl_init();

        $sales = [
            "paymentMode" => $_paiement
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-AUTH:" . $_x_auth
            ],
            CURLOPT_POSTFIELDS => json_encode($sales),
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/demat/api/v1/agents/" . $_agent_ref . "/sales/" . $_sale_ref . "/confirm",
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/demat/api/v1/agents/".$_agent_ref."/sales/".$_sale_ref."/confirm",
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

    public function getSales($_x_auth, $_agent_ref, $_sale_ref)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "X-AUTH:" . $_x_auth
            ],
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/demat/api/v1/agents/" . $_agent_ref . "/sales/" . $_sale_ref,
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/demat/api/v1/agents/".$_agent_ref."/sales/".$_sale_ref,
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

    public function cancellingSale($_x_auth, $_agent_ref, $_sale_ref, $_serial_number, $_motif)
    {
        $curl = curl_init();

        $sales = [
            "motive" => $_motif,
            "serialNumbers" => [$_serial_number]
        ];

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "X-AUTH:" . $_x_auth
            ],
            CURLOPT_POSTFIELDS => json_encode($sales),
            // prod
            CURLOPT_URL => "https://api-server.aleda.fr/demat/api/v1/agents/" . $_agent_ref . "/sales/" . $_sale_ref . "/cancelling",
            // test
            //CURLOPT_URL => "https://iapi-server.aleda.fr/demat/api/v1/agents/".$_agent_ref."/sales/".$_sale_ref."/cancelling",
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
}
