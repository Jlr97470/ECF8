<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProposeThemeController extends AbstractController
{
    #[Route('/propose/theme', name: 'app_propose_theme')]
    public function index(): Response
    {
        return $this->render('propose_theme/index.html.twig', [
            'controller_name' => 'ProposeThemeController',
        ]);
    }
}
