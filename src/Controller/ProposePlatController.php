<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProposePlatController extends AbstractController
{
    #[Route('/propose/plat', name: 'app_propose_plat')]
    public function index(): Response
    {
        return $this->render('propose_plat/index.html.twig', [
            'controller_name' => 'ProposePlatController',
        ]);
    }
}
