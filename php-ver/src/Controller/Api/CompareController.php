<?php

namespace App\Controller\Api;

use App\Repository\TransfertRepository;
use App\Service\UploadFileService;
use App\Service\Api\ServiceDing;
use App\Service\Api\ServiceReloadly;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompareController extends AbstractController
{

	public function __construct()
    {
        
    }

}