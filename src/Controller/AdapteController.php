<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdapteController extends AbstractController
{
    #[Route('/adapte', name: 'app_adapte')]
    public function index(): Response
    {
        return $this->render('adapte/index.html.twig', [
            'controller_name' => 'AdapteController',
        ]);
    }
}
