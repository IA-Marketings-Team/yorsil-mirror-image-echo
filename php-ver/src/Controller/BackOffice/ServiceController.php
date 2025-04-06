<?php

namespace App\Controller\BackOffice;

use App\Entity\Services;
use App\Entity\ServicesBout;
use App\Form\BackOffice\ServicesType;
use App\Repository\BoutRepository;
use App\Repository\ServicesBoutRepository;
use App\Repository\ServicesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ServiceController extends AbstractController
{
    private $_entity_manager;
    private $serviceRepo;
    private $serviceBoutRepo;
    private $boutRepo;

    public function __construct(
        EntityManagerInterface $_entity_manager,
        ServicesRepository $serviceRepo,
        ServicesBoutRepository $serviceBoutRepo,
        BoutRepository $boutRepo)
    {
        $this->_entity_manager = $_entity_manager;
        $this->serviceRepo     = $serviceRepo;
        $this->serviceBoutRepo = $serviceBoutRepo;
        $this->boutRepo        = $boutRepo;
    }

    /**
     * @Route("/liste-services", name="liste_services")
     */
    public function listeServices($mdp = null): Response
    {
    	if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        if($this->isGranted('ROLE_DEV')){
            return $this->render('BackOffice/service/liste.html.twig', [
                'services' => $this->serviceRepo->findAll()
            ]);
        } else if ($this->isGranted('ROLE_ADMIN')) {
            // return $this->redirectToRoute('liste_services_bout');
            return $this->render('BackOffice/service/liste.html.twig', [
                'services' => $this->serviceRepo->findAll()
            ]);
        } else {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!"
            );
            return $this->redirectToRoute('tableau_de_bord');
        } 
    }

    /**
     * @Route("/modifier-service/{id}", name="edite_service")
     */
    public function editeService(Request $request, Services $service)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        // if ($this->isGranted('ROLE_DEV')) {
        if ($this->isGranted('ROLE_ADMIN')) {
            $form = $this->createForm(ServicesType::class, $service);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $modules = [];
                foreach ($request->request->all() as $key => $value) {
                    if (strpos($key, 'module-') === 0) {
                        $modules[] = $value;
                    }
                }

                $service->setModule($modules);

                $this->_entity_manager->persist($service);
                $this->_entity_manager->flush();

                $this->addFlash(
                    'success',
                    "Le service {$service->getNom()} a été bien modifiée"
                );

                return $this->redirectToRoute('liste_services');
            }

            return $this->render('BackOffice/service/edite.html.twig', [
                'form'     => $form->createView(),
                'services' => $service
            ]);
        // } else if ($this->isGranted('ROLE_ADMIN')) {
        //     return $this->redirectToRoute('liste_services_bout');
        } else {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!"
            );
            return $this->redirectToRoute('tableau_de_bord');
        }
    }

    /**
     * @Route("/supprimer-service/{id}", name="delete_service")
     */
    public function deleteService(Request $request, $id)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_DEV')) {
            $service = $this->serviceRepo->findOneById($id);

            if ($service) {

                $this->_entity_manager->remove($service);
                $this->_entity_manager->flush();

                $this->addFlash(
                    'success',
                    "Le service {$service->getNom()} a été bien supprimée"
                );
            } else {
                $this->addFlash(
                    'warning',
                    "cette service n'existe pas"
                );
            }

            return $this->redirectToRoute('liste_services');
        } else if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('liste_services_bout');
        } else {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!"
            );
            return $this->redirectToRoute('tableau_de_bord');
        }
    }

    /**
     * @Route("/ajouter-nouveau-service", name="new_service")
     */
    public function newService(Request $request)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $service = new Services();
        $form = $this->createForm(ServicesType::class, $service);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $modules = [];
            foreach ($request->request->all() as $key => $value) {
                if (strpos($key, 'module-') === 0) { // Vérifie si le nom commence par "module-"
                    $modules[] = $value;
                }
            }

            $service->setModule($modules);

            $this->_entity_manager->persist($service);
            $this->_entity_manager->flush();

            $this->addFlash(
                'success',
                "Le service {$service->getNom()} a été bien enregistrée"
            );

            return $this->redirectToRoute('liste_services');
        }

        return $this->render('BackOffice/service/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/liste-services-boutique", name="liste_services_bout")
     */
    public function listeServicesBout(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->render('BackOffice/service/listeServiceBout.html.twig', [
                'services' => $this->serviceBoutRepo->findAll()
            ]);
        } else {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!"
            );
            return $this->redirectToRoute('tableau_de_bord');
        }
    }

    /**
     * @Route("/ajouter-services-boutique", name="add_services_bout")
     */
    public function addServicesBout(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            if ($request->isXmlHttpRequest()) {
                $data = json_decode($request->getContent(), true);

                $bout = $this->boutRepo->findOneById($data['boutique']);

                $serviceBout = new ServicesBout();
                $serviceBout->setBoutique($bout);
                $serviceBout->setService($data['services']);

                $this->_entity_manager->persist($serviceBout);
                $this->_entity_manager->flush();

                return new JsonResponse(['status' => true], 200);
            }
            return $this->render('BackOffice/service/addServiceBout.html.twig', [
                'services' => $this->serviceRepo->findAll(),
                'boutiques' => $this->boutRepo->findAll()
            ]);
        } else {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!"
            );
            return $this->redirectToRoute('tableau_de_bord');
        }
    }

    /**
     * @Route("/modifier-services-boutique/{id}", name="edite_services_bout")
     */
    public function editeServicesBout(Request $request, ServicesBout $serviceBout = null): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            if (!$serviceBout) {
                $this->addFlash(
                    'warning',
                    "Cette service n'existe pas !"
                );
                return $this->redirectToRoute('liste_services_bout');
            }

            $bout = $this->boutRepo->findOneById($serviceBout->getBoutique());
            $services = array_map(function ($service) {
                return [
                    'id' => $service->getId(),
                    'nom' => $service->getNom(),
                    "status" => true,
                    'module' => $service->getModule(),
                ];
            }, $this->serviceRepo->findAll());

            $servicesAssoc = [];
            foreach ($services as $service) {
                $servicesAssoc[$service['id']] = $service['module'] ?? [];
            }

            $boutService = $serviceBout->getService();

            $boutService = array_map(
                function ($item) use ($servicesAssoc) {
                    $id = $item['id'];
                    $modulesRef = $servicesAssoc[$id] ?? []; // Modules de référence
                    $existingModules = array_column($item['modules'], 'nom'); // Modules actuels

                    // Supprimer les modules non présents dans $services
                    $item['modules'] = array_filter($item['modules'], function ($module) use ($modulesRef) {
                        return in_array($module['nom'], $modulesRef);
                    });

                    // Ajouter les modules manquants avec status = true
                    foreach ($modulesRef as $module) {
                        if (!in_array($module, $existingModules)) {
                            $item['modules'][] = ["nom" => $module, "status" => true];
                        }
                    }

                    return $item;
                },
                $boutService
            );

            if ($request->isXmlHttpRequest()) {
                $data = json_decode($request->getContent(), true);

                $serviceBout->setService($data['services']);

                $this->_entity_manager->persist($serviceBout);
                $this->_entity_manager->flush();

                return new JsonResponse(['status' => true], 200);
            }

            return $this->render('BackOffice/service/editeServiceBout.html.twig', [
                'serviceBout'  => $serviceBout,
                'services'  => $boutService,
                'boutique' => $bout
            ]);
        } else {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!"
            );
            return $this->redirectToRoute('tableau_de_bord');
        }
    }

    /**
     * @Route("/supprimer-service-bout/{id}", name="delete_service_bout")
     */
    public function deleteServiceBout(Request $request, $id)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $service = $this->serviceBoutRepo->findOneById($id);

        if ($service) {

            $this->_entity_manager->remove($service);
            $this->_entity_manager->flush();

            $this->addFlash(
                'success',
                "Le service a été bien supprimée"
            );
        } else {
            $this->addFlash(
                'warning',
                "cette service n'existe pas"
            );
        }

        return $this->redirectToRoute('liste_services_bout');
    }


    /**
     * @Route("/verif-services-boutique", name="verif_services_bout")
     */
    public function verifServicesBout(Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            if ($request->isXmlHttpRequest()) {
                $data = json_decode($request->getContent(), true);

                $bout = $this->boutRepo->findOneById($data['boutique']);

                $services = array_map(function ($service) {
                    return [
                        'id' => $service->getId(),
                        'nom' => $service->getNom(),
                        "status" => true,
                        'module' => $service->getModule(),
                    ];
                }, $this->serviceRepo->findAll());

                $servicesBout = $this->serviceBoutRepo->findOneByBoutique($bout);

                if($servicesBout !== null) {

                    $boutService = $servicesBout->getService();

                    $servicesAssoc = [];
                    foreach ($services as $service) {
                        $servicesAssoc[$service['id']] = $service['module'] ?? [];
                    }

                    $boutService = array_map(function ($item) use ($servicesAssoc) {
                        $id = $item['id'];
                        $modulesRef = $servicesAssoc[$id] ?? []; // Modules de référence
                        $existingModules = array_column($item['modules'], 'nom'); // Modules actuels

                        // Supprimer les modules non présents dans $services
                        $item['modules'] = array_filter($item['modules'], function ($module) use ($modulesRef) {
                                return in_array($module['nom'], $modulesRef);
                            });

                        // Ajouter les modules manquants avec status = true
                        foreach ($modulesRef as $module) {
                            if (!in_array($module, $existingModules)) {
                                $item['modules'][] = ["nom" => $module, "status" => true];
                            }
                        }

                        return $item;
                    },
                        $boutService
                    );

                    if ($servicesBout) {
                        return new JsonResponse(
                            [
                                'status'       => true,
                                'services'     => $boutService,
                                'servicesBout' => $servicesBout->getId()
                            ], 200
                        );
                    }
                }

                return new JsonResponse(
                    [
                        'status'   => false,
                        'services' => $services
                    ], 200
                );
            }
            return $this->redirectToRoute('liste_services_bout');
        } else {
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!"
            );
            return $this->redirectToRoute('tableau_de_bord');
        }
    }

}
