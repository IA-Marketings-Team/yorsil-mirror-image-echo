<?php

namespace App\Controller\FrontOffice;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PolitiqueCGVController extends AbstractController
{

    /**
     * @Route("/politique_de_confidentialite", name="politique_de_confidentialite")
     * @IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function dashboard()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('FrontOffice/yorsil/politiqueConfidentialite.html.twig');
        // return $this->render('FrontOffice/politique_de_confidentialite.html.twig');
    }

    /**
     * @Route("/conditions_generales_utilisation", name="conditions_generales_utilisation")
     * @IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function conditionsGeneralesUtilisation()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('FrontOffice/yorsil/ConditionsGeneralesUtilisation.html.twig');
    }

    /**
     * @Route("/mentions-legales", name="mentions_legales")
     * @IsGranted("ROLE_BOUT", message="Vous ne pouvez pas accéder sur cette url, sera réserve pour le compte boutique!")
     */
    public function mentionsLegales()
    {
        if (!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('FrontOffice/yorsil/MentionsLegales.html.twig');
    }
}
