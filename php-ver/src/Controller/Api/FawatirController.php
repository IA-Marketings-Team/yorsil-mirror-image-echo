<?php 

namespace App\Controller\Api;

use App\Entity\Debit;
use App\Entity\TauxChange;
use App\Entity\FatouratiCreances;
use App\Entity\FatouratiCreanciers;
use App\Entity\FatouratiPaiement;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\BoutRepository;
use App\Repository\FatouratiCreancesRepository;
use App\Repository\FatouratiCreanciersRepository;
use App\Repository\FatouratiPaiementRepository;
use App\Repository\TauxChangeRepository;
use App\Service\Metier\ServiceMetierBoutique;
use App\Service\SoapClientService;
use App\Service\SoapServiceGuzzle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class FawatirController extends AbstractController
{

    private $soapClientService;
    private $soapServiceGuzzle;
    private $_boutique_manager;

    public function __construct(EntityManagerInterface $_entity_manager,SoapClientService $soapClientService,SoapServiceGuzzle $soapServiceGuzzle, ServiceMetierBoutique $_boutique_manager,FatouratiCreancesRepository $fatouratiCreancesRepository,TauxChangeRepository $tauxChangeRepository,FatouratiCreanciersRepository $fatouratiCreanciersRepository,FatouratiPaiementRepository $fatouratiPaiementRepository, BoutRepository $boutRepository)
    {
        $this->soapClientService = $soapClientService;
        $this->soapServiceGuzzle = $soapServiceGuzzle;
        $this->_boutique_manager = $_boutique_manager;
        $this->_boutique_repository = $boutRepository;
        $this->_entity_manager   = $_entity_manager;
        $this->fatouratiCreancesRepository   = $fatouratiCreancesRepository;
        $this->fatouratiCreanciersRepository = $fatouratiCreanciersRepository;
        $this->fatouratiPaiementRepository   = $fatouratiPaiementRepository;
        $this->tauxChangeRepository          = $tauxChangeRepository;
    }

    public function generateHash(string $input): string
    {
        // Convertir la chaîne de caractères en bytes (utilisation de UTF-8)
        $defaultBytes = mb_convert_encoding($input, 'UTF-8');

        // Créer un hachage MD5
        $md5Hash = md5($defaultBytes, true); // true pour un résultat binaire brut

        // Encoder le résultat en Base64
        return base64_encode($md5Hash);
    }

    /**
     * @Route("/service", name="service")
     */
    public function choixService(Request $request): Response
    {
        $_creance_id   = $request->request->get('creance_id');
        $_creancier_id = $request->request->get('creancier_id');

        $aquerreurID = '823';          
        $canalID = '2';                
        $dateServeur = '20190903100000';     
        $modeID = '2';                  
        $creancierID = $_creancier_id;        
        $creanceID = $_creance_id;  
        $refTxSysPmt = '';     
        $typeCanal = '7';              
        $cleSecrete = '05CB9DE276B33DB8FE108980F15543F1';    

        $chaine = $aquerreurID . $canalID . $creanceID . $creancierID .$dateServeur . $modeID . $refTxSysPmt . $typeCanal . $cleSecrete;

        $gen     = $this->generateHash($chaine);
        $gencode = base64_decode($gen);

        $params = [
            'in0' => [
                'MAC' =>$gencode,
                'aquereurID' => '823',
                'canalID' => '2',
                'creanceID' => $creanceID,
                'creancierID' => $creancierID,
                'dateServeur' => '20190903100000',
                'modeID' => '2',
                'refTxSysPmt' => '',
                'typeCanal' => '7'
            ]
        ];

        try {
            $response = $this->soapClientService->getForms($params);
        //dd($response);
            // Passer la réponse au template Twig
            return $this->render('FrontOffice/fawatir/forms.html.twig', [
                'response' => $response,
                'creanceId' => $_creance_id,
                'creancierId' => $_creancier_id
            ]);

        } catch (\Exception $e) {
            return new Response("Erreur lors de l'appel SOAP : " . $e->getMessage());
        }
    
    }

    /**
     * @Route("/{id}/liste-creances", name="liste_creances")
     */
    public function listeCreances(Request $request,String $id): Response
    {
        // Variables
        $aquerreurID = '823';          
        $canalID = '2';                
        $dateServeur = '20201203051950';     
        $modeID = '2';                  
        $creancierID = $id;        
        $refTxSysPmt = '';     
        $typeCanal = '7';              
        $cleSecrete = '05CB9DE276B33DB8FE108980F15543F1';    

        $chaine = $aquerreurID . $canalID . $dateServeur . $modeID . $creancierID . $refTxSysPmt . $typeCanal . $cleSecrete;

        $gen     = $this->generateHash($chaine);
        $gencode = base64_decode($gen);
        $params = [
            'in0' => [
                'MAC' => $gencode,
                'aquereurID' => '823',
                'canalID' => '2',
                'categorieCreance' => '0',
                'creancierID' => $id,
                'dateServeur' => '20201203051950',
                'modeID' => '2',
                'refTxSysPmt' => '',
                'typeCanal' => '7'
            ]
        ];

        try {
            $response = $this->soapClientService->getListCreances($params);
            $_creancier = $this->fatouratiCreanciersRepository->findOneBy(['code_creancier' => $creancierID]);
            if($response){
                if($response->nbreCreance > 0){
                    if(is_iterable($response->listeCreances->item)){
                        foreach($response->listeCreances->item as $_creance){
                            $_creance_rec = $this->fatouratiCreancesRepository->findOneBy(['creancier' => $_creancier,'code_creance' => $_creance->codeCreance ]);
                            if (!$_creance_rec) {
                                $_liste_creances = new FatouratiCreances();
                                $_liste_creances->setCodeCreance($_creance->codeCreance);
                                $_liste_creances->setNomCreance($_creance->nomCreance);
                                $_liste_creances->setDateAjout(new \DateTime());
                                $_liste_creances->setCreancier($_creancier);
                                $this->_entity_manager->persist($_liste_creances);
                                $this->_entity_manager->flush();
                            }
                        } 
                    }else{

                        $_creance_rec = $this->fatouratiCreancesRepository->findOneBy(['creancier' => $_creancier,'code_creance' => $response->listeCreances->item->codeCreance ]);
                        //dd($response,$_creance_rec);
                        if (!$_creance_rec) {
                            $_liste_creances = new FatouratiCreances();
                            $_liste_creances->setCodeCreance($response->listeCreances->item->codeCreance);
                            $_liste_creances->setNomCreance($response->listeCreances->item->nomCreance);
                            $_liste_creances->setDateAjout(new \DateTime());
                            $_liste_creances->setCreancier($_creancier);
                            $this->_entity_manager->persist($_liste_creances);
                            $this->_entity_manager->flush();
                        }
                    }    
                }
            }

            // Passer la réponse au template Twig
            return $this->render('FrontOffice/fawatir/creances.html.twig', [
                'response' => $response,
                'creancierId' => $id
            ]);

        } catch (\Exception $e) {
            return new Response("Erreur lors de l'appel SOAP : " . $e->getMessage());
        }
    
    }

    /**
     * @Route("/liste-creanciers", name="liste_creanciers")
     */
    public function listeCreanciers(): Response
    {

        $aquerreurID = '823';          
        $canalID = '2';                
        $dateServeur = '20190903100000';     
        $modeID = '2';                  
        //$creancierID = '1009';        
        $refTxSysPmt = '';     
        $typeCanal = '7';              
        $cleSecrete = '05CB9DE276B33DB8FE108980F15543F1';    

        $chaine  = $aquerreurID . $canalID . $dateServeur . $modeID  . $refTxSysPmt . $typeCanal . $cleSecrete;
        $gen     = $this->generateHash($chaine);
        $gencode = base64_decode($gen);

        $data = [
            'frnreq' => [
                'MAC' => $gencode,
                'aquereurID' => '823',
                'canalID' => '2',
                'categorieCreance' => '000',
                'dateServeur' => '20190903100000',
                'modeID' => '2',
                'refTxSysPmt' => '',
                'typeCanal' => '7',
            ]
        ];

        try {
            $response = $this->soapClientService->getListCreanciers($data);
            if($response){
                if($response->nbreCreancier > 0){
                    foreach($response->listeCreanciers->item as $_creancier){
                        $_creancier_rec = $this->fatouratiCreanciersRepository->findOneBy(['code_creancier' => $_creancier->codeCreancier ]);
                        if (!$_creancier_rec) {
                            $_liste_creanciers = new FatouratiCreanciers();
                            $_liste_creanciers->setCodeCreancier($_creancier->codeCreancier);
                            $_liste_creanciers->setNomCreancier($_creancier->nomCreancier);
                            $_liste_creanciers->setDateAjout(new \DateTime());
                            $this->_entity_manager->persist($_liste_creanciers);
                            $this->_entity_manager->flush();
                        }
                    }     
                }
            }

            // Passer la réponse au template Twig
            return $this->render('FrontOffice/fawatir/creanciers.html.twig', [
                'response' => $response
            ]);

        } catch (\Exception $e) {
            return new Response("Erreur lors de l'appel SOAP : " . $e->getMessage());
        }
    
    }

    /**
     * @Route("/impayes", name="impayes")
     */
    public function listeImpayes(request $request, SessionInterface $session): Response
    {

        $_creancier_id = $request->request->get('creancier_id');        
        $_creance_id   = $request->request->get('creance_id');
        $_nbr_params   = $request->request->get('nbr_params'); 
        $_vals = '';
        $_article_array = [];
        for ($i=0; $i < $_nbr_params ; $i++) { 
            $_nom_champ = $request->request->get('nom_champ_'.$i); 
            $_val_champ = $request->request->get('val_champ_'.$i); 
            $_vals .= $_nom_champ.$_val_champ;

            $_article_array[] = [
                'nom_champ' => $_nom_champ,
                'val_champ' => $_val_champ
            ];
        }    

        $aquerreurID = '823';          
        $canalID = '2';                
        $dateServeur = '20190903100000';     
        $modeID = '2';            
        $refTxSysPmt = '';     
        $typeCanal = '7';              
        $cleSecrete = '05CB9DE276B33DB8FE108980F15543F1';   

        $chaine  = $aquerreurID . $canalID . $_vals . $_creance_id . $_creancier_id . $dateServeur . $modeID  . $refTxSysPmt . $typeCanal . $cleSecrete;

        $gen     = $this->generateHash($chaine);
        $gencode = base64_decode($gen);

        try {
            $response = $this->soapServiceGuzzle->getImpayes($gen,$_creance_id,$_creancier_id,$_article_array);
            $impayes = [
                'response' => $response[0],
                'montantTTC' => $response[1],
                'refTxFatourati' => $response[2],
                'creanceId' => $_creance_id,
                'creancierId' => $_creancier_id
            ];

            $session->set("impayes", json_encode($impayes));

            // Passer la réponse au template Twig
            return $this->render('FrontOffice/fawatir/impayes.html.twig', [
                'response' => $response[0],
                'montantTTC' => $response[1],
                'refTxFatourati' => $response[2],
                'creanceId' => $_creance_id,
                'creancierId' => $_creancier_id
            ]);

        } catch (\Exception $e) {
            return new Response("Erreur lors de l'appel SOAP : " . $e->getMessage());
        }
    }
    
    /**
     * @Route("/confirmation", name="confirmation")
     */
    public function confirmation(request $request, SessionInterface $session): Response
    {

        $allData = $request->request->all();
        $_article_array = [];
        $_concat_id = '';
        $_total = 0.0;
        // Vérifiez si article_id existe et est un tableau
        if (isset($allData['article_id']) && is_array($allData['article_id'])) {
            foreach ($allData['article_id'] as $item) {
                $parts = explode("/", $item);

                $_article_array[] = [
                    'id' => $parts[0],
                    'prix' => (float)$parts[1],
                    'type' => (int)$parts[2] // Converti en entier si nécessaire
                ];

                $_concat_id .= $parts[0] . ""; // Concaténation des IDs
                $_total += (float)$parts[1]; // Somme des valeurs
            }
        }

        $_bout     = $this->getUser();
        $_boutique = $this->_boutique_repository->findOneBy(["user" => $_bout]);

        $_credit = $this->_boutique_manager->creditBoutique($_bout);
        $_debit  = $this->_boutique_manager->debitBoutique($_bout);
        $_geste  = $this->_boutique_manager->gesteBoutique($_bout);
        $_solde  = (float)($_credit + $_geste - $_debit);
        $_solde  = number_format($_solde, 2, '.', '');

        $_taux   = $this->tauxChangeRepository->findOneBy(['devise' => 'MAD'],['date_change' => 'DESC']);
        $_devise = ($_taux) ? round(($_total/$_taux->getMontantChange()),2) : round(($_total/11),2);
// dd($_total,$_taux,$_devise,$_solde,$_article_array);
        if($_devise > $_solde) {
            $this->addFlash(
                'warning',
                "Votre solde actuel ne permet pas de réaliser ce paiement. Veuillez recharger votre compte."
            );

            $impayes = $session->get("impayes", null);
            if ($impayes) {
                $impayes = json_decode($impayes, true);
                return $this->render('FrontOffice/fawatir/impayes.html.twig', [
                    'response' => $impayes['response'] ?? null,
                    'montantTTC' => $impayes['montantTTC'][0] ?? null,
                    'refTxFatourati' => $impayes['refTxFatourati'][0] ?? null,
                    'creanceId' => $impayes['creanceId'] ?? null,
                    'creancierId' => $impayes['creancierId'] ?? null,
                ]);
            } else {
                return $this->redirectToRoute('liste_creanciers');
            }
        }

        $refTxFatourati = $request->request->get('ref');
        $_creancier_id = $request->request->get('creancier_id');        
        $_creance_id = $request->request->get('creance_id'); 
        $codeAutorisation ='190799';
        $codeDevise='504';
        $codeReconciliation='00';
        $aquerreurID = '823';          
        $canalID = '2';                
        $dateServeur = '20201126044712';     
        $modeID = '2';            
        $refTxSysPmt = '20190903100000234';     
        $typeCanal = '7';              
        $cleSecrete = '05CB9DE276B33DB8FE108980F15543F1';  
        $paiement ='0'; // 1 si c'est un paiement 
        $empreint = '0000';

        // $chaine  = '101' . '2' . '190799' . '504' . '00' . '01' . '1008' . '20201126044712' . '209238999'.'Frais' . '3' . '69.88' . '20201126044712962' . '100001183731' . '2' . '05CB9DE276B33DB8FE108980F15543F1';
        $chaine = $aquerreurID . $canalID . $codeAutorisation . $codeDevise . $codeReconciliation . $_creance_id . $_creancier_id . $dateServeur . $empreint . $_concat_id .  $modeID . $_total . $refTxSysPmt . $refTxFatourati . $typeCanal . $cleSecrete;
        $gen     = $this->generateHash($chaine);
        // Aquerreur ID + Canal ID + CodeAutorisation + CodeDevise + CodeReconciliation + Creance ID + Creancier ID + Date Serveur + EmpreintPaiement + la concatenation des id des articles + ModeID + Montant Total TTC + RefTxSysPmt + RefTxFatourati + TypeCanal + cle Secrete

        $response = $this->soapServiceGuzzle->setConfirmation($gen,$_article_array,$refTxFatourati,$_creancier_id,$_creance_id,$refTxSysPmt,$paiement,$_total);
        

        $_creancier = $this->fatouratiCreanciersRepository->findOneBy(['code_creancier' => $_creancier_id]);
        $_creance   = $this->fatouratiCreancesRepository->findOneBy(['creancier' => $_creancier,'code_creance' => $_creance_id]);

        if($response){
            if ($response->codeRetour == "000") {
                $_paiement = new FatouratiPaiement();
                $_paiement->setCreancier($_creancier);
                $_paiement->setCreance($_creance);
                $_paiement->setRefTxSysPmt($refTxSysPmt);
                $_paiement->setRefTxFatourati($refTxFatourati);
                $_paiement->setMontantTTC($_total);
                $_paiement->setMontantDevise($_devise);
                $_paiement->setListeArticleSelectionne($_article_array);
                $_paiement->setDateAjout(new \DateTime());
                $_paiement->setTauxChange($_taux);
                $_paiement->setBoutique($_boutique);

                $_desc = "Paiement facture de $_devise €";
                $_debits = new Debit();
                $_debits->setDate(new \Datetime());
                $_debits->setMontant($_devise);
                $_debits->setBout($_boutique);
                $_debits->setAdmin(null);
                $_debits->setNote($_desc);

                $this->_entity_manager->persist($_debits);
                $this->_entity_manager->flush();

                $this->_entity_manager->persist($_paiement);
                $this->_entity_manager->flush();

                $this->addFlash(
                    'success',
                    "Paiement de facture effectué avec succès"
                );
            }
        } else{
            $this->addFlash(
                'success',
                "Paiement de facture  non effectué"
            );
        }

        return $this->render('FrontOffice/fawatir/confirmation.html.twig', [
            'response' => $response
        ]);
    }
    
    /**
     * @Route("/{id}/annulation", name="annulation")
     */
    public function annulation(request $request, String $id): Response
    {
        $allData = $request->request->all();
    
        $_paiement = $this->fatouratiPaiementRepository->findOneBy(['id' => $id]);
        $_bout     = $this->getUser();
        $_boutique = $this->_boutique_repository->findOneBy(["user" => $_bout]);

        $_concat_id = '';
        $_creancier_id = $_paiement->getCreancier()->getCodeCreancier();
        $_creance_id = $_paiement->getCreance()->getCodeCreance();
        $refTxSysPmt = $_paiement->getRefTxSysPmt();
        $refTxFatourati = $_paiement->getRefTxFatourati();
        $_total = $_paiement->getMontantTTC();
        $_article_array = $_paiement->getListeArticleSelectionne();

        for($_i = 0; $_i < count($_article_array) ; $_i++) {
            $_concat_id .= $_article_array[$_i]['id'] . "";
        }

    //dd($id,$_paiement,$_article_array,$_concat_id);

        $codeAutorisation ='190799';
        $codeDevise='504';
        $codeReconciliation='00';
        $aquerreurID = '823';          
        $canalID = '2';                
        $dateServeur = '20201126044712';     
        $modeID = '2';            
        //$refTxSysPmt = '20190903100000234';     
        $typeCanal = '7';              
        $cleSecrete = '05CB9DE276B33DB8FE108980F15543F1';  
        $paiement ='0'; // 1 si c'est un paiement 
        $empreint = '0000';

        $chaine = $aquerreurID . $canalID . $codeAutorisation . $codeDevise . $codeReconciliation . $_creance_id . $_creancier_id . $dateServeur . $empreint . $_concat_id .  $modeID . $_total . $refTxSysPmt . $refTxFatourati . $typeCanal . $cleSecrete;
        $gen     = $this->generateHash($chaine);
        $response = $this->soapServiceGuzzle->setAnnulation($gen,$_article_array,$refTxFatourati,$_creancier_id,$_creance_id,$refTxSysPmt,$paiement,$_total);

        if ($response->codeRetour == "000") {
            $_paiement->setDateEnvoieAnnulation(new \DateTime());
            $_paiement->setAnnulation('1');
            $this->_entity_manager->flush();
        }
    
        return $this->render('BackOffice/journal/annulation_paiement.html.twig', [
            'response' => $response
        ]);
    }

    

}