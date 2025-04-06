<?php

namespace App\Controller\BackOffice;

use App\Entity\Slide;
use App\Form\BackOffice\SlideType;
use App\Repository\SlideRepository;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class SliderController extends AbstractController
{
    private $uploadFileService;

    public function __construct(UploadFileService $uploadFileService)
    {
        $this->uploadFileService = $uploadFileService;
    }

    /**
     * @Route("/liste-des-slides", name="liste_slides")
     * @IsGranted("ROLE_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réservé au Super-administrateur!")
     */
    public function SlideLists(SlideRepository $slideRepository): Response
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('BackOffice/slider/listes.html.twig', [
            'sliders' => $slideRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajouter-slider", name="ajout_slider")
     * @IsGranted("ROLE_SUPER_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réservé au Super-administrateur!")
     */
    public function AjoutSlider(Request $request,EntityManagerInterface $manager)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $slide = new Slide();

        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->uploadFileService->uploadFile($form, $slide, "image", "upload_images_slider");
            
            $manager->persist($slide);
            $manager->flush();
            
            $this->addFlash(
                'success',
                "Le slide a été bien enregistré"
            );
            return $this->redirectToRoute('liste_slides');
        }

        return $this->render('BackOffice/slider/ajout.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/slider/modification/{id}", name="edit_slider")
     * @IsGranted("ROLE_SUPER_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réservé au Super-administrateur!")
     */
    public function editSlider(Request $request, EntityManagerInterface $manager, Slide $slide)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(SlideType::class, $slide);
        $form->handleRequest($request);
        if($form->isSubmitted())
        {
            $this->uploadFileService->uploadFile($form, $slide, "image", "upload_images_slider");

            $manager->persist($slide);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le slide a été modifiés avec succès !"
            );

            return $this->redirectToRoute('liste_slides');

        }

        return $this->render('BackOffice/slider/edit.html.twig',[
            'form' => $form->createView(),
            'slider' => $slide
        ]);
    }

    /** 
     * @Route("/slider/delete/{id}", name="slider_delete")
     * @IsGranted("ROLE_SUPER_ADMIN", message="Vous ne pouvez pas accéder sur cette url, sera réserve à l’Administrateur!")
     */
    public function deleteSlide(EntityManagerInterface $manager,Slide $slide)
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }
        
        $manager->remove($slide);
        $manager->flush();

        return new JsonResponse([
            'status'    => true
        ]);
    }


}




