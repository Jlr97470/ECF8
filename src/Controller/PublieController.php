<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PublieController extends AbstractController
{
    #[Route('/publie', name: 'app_publie')]
    public function index(): Response
    {
        return $this->render('publie/index.html.twig', [
            'controller_name' => 'PublieController',
        ]);
    }
}
