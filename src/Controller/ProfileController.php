<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;


use App\Entity\Utilisateur;
use App\Form\ProfileType;

class ProfileController extends AbstractController
{

    #[Route('/profile/index', name: 'app_profile_index')]
    public function index(EntityManagerInterface $em): Response
    {
        // On récupère l'profile qui correspond à l'id passé dans l'url
        $user = $this->getUser();
        $profile = $em->getRepository(Utilisateur::class)->findOneBy(['utilisateur_id' => method_exists($user, 'getUtilisateurId') ? $user->getUtilisateurId() : $user->getId()]);

        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $mode = 'update';
        // On récupère l'profile qui correspond à l'id passé dans l'url
        $user = $this->getUser();
        $profile = $em->getRepository(Utilisateur::class)->findOneBy(['utilisateur_id' => method_exists($user, 'getUtilisateurId') ? $user->getUtilisateurId() : $user->getId()]);

        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) { 
            $profile->setPassword(
                $passwordHasher->hashPassword(
                    $profile,
                    $form->get('password')->getData()
                )
            );                       
            $this->saveprofile($profile, $mode,$em);

            return $this->redirectToRoute('app_profile_index');
        }

        $parameters = array(
            'form'      => $form->createView(),
            'profile'      => $profile,
            'mode'      => $mode
        );

        return $this->render('profile/edit.html.twig', $parameters);
    }

    /**
     * Enregistrer un profile en base de données
     * 
     * @param   Utilisateur $profile
     * @param   string      $mode 
     */
    private function saveprofile(Utilisateur $profile, string $mode, EntityManagerInterface $em){
        $em->persist($profile);
        $em->flush();

        $this->addFlash('success', 'Profile mis à jour avec succès');
    }    
}
