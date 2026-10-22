<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContientController extends AbstractController
{
    #[Route('/contient', name: 'app_contient')]
    public function index(): Response
    {
        return $this->render('contient/index.html.twig', [
            'controller_name' => 'ContientController',
        ]);
    }
}
