<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PossedeController extends AbstractController
{
    #[Route('/possede', name: 'app_possede')]
    public function index(): Response
    {
        return $this->render('possede/index.html.twig', [
            'controller_name' => 'PossedeController',
        ]);
    }
}
