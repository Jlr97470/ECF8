<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RoleController extends AbstractController
{
    #[Route('/role', name: 'app_role')]
    public function index(): Response
    {
        return $this->render('role/index.html.twig', [
            'controller_name' => 'RoleController',
        ]);
    }
}
