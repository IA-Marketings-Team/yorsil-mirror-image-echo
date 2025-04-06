<?php

namespace App\Controller\BackOffice;

use App\Entity\Pays;
use App\Repository\PaysRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaysController extends AbstractController
{

    public function __construct(
        EntityManagerInterface $_entity_manager,
        PaysRepository $paysRepository
    )
    {
        $this->_entity_manager = $_entity_manager;
        $this->_pays_repository = $paysRepository;
    }

    /**
     * @Route("/liste-des-pays", name="pays")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function index(): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $_is_all = false ;
        $_pays   = $this->_pays_repository->findAll(); 
        
        for ($i=0; $i < count($_pays) ; $i++) { 
            $_full_true = true ;
            if($_pays[$i]->getIsApi()==true){
                $_full_true = true ;
            } else {
                $_full_true = false ;
            }    
        }    

        $_is_all = $_full_true ;

        return $this->render('BackOffice/pays/index.html.twig', [
            'pays' => $this->_pays_repository->findAll(),
            'all_check' => $_is_all
        ]);
    }

     /**
     * @Route("/modification-pays", name="modification_pays")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function updatePays(Request $request): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
    
        $_array = $request->request->get('iso');
        $_id    = $request->request->get('ids');
        $_api   = ($request->request->get('api') == '') ? 'diaspo' : $request->request->get('api');

        for ($i=0; $i < count($_array); $i++) { 
            $_pays  = $this->_pays_repository->findOneBy([ 'id' => $_id[$i] ]);
            $_pays->setIsApi($_array[$i]);
            if($_array[$i] == '1'){
                $_pays->setTypeApi($_api);
            }else{
                if ($_pays->getTypeApi() == $request->request->get('api')) {
                    $_pays->setTypeApi(null);
                }
            }
            $this->_entity_manager->flush();
        }

        $this->addFlash(
            'success',
            "Modificication effectuée avec succès"
        );

       return $this->redirectToRoute('pays');
    }


    /**
     * @Route("/filtre-pays", name="filtre_pays")
    */
    public function flitrePays(Request $request){
        $_api  = $request->request->get('api');
        $_pays = $this->_pays_repository->findByApi($_api,$request->request->get('nom'));
        

        return new JsonResponse([
            'html' => $this->renderView('BackOffice/pays/result.html.twig', [
                "pays" => $_pays, 
                "api" => $_api
            ])
        ]);
    }

}