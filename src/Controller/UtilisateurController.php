<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use App\Entity\Utilisateur;
use App\Entity\Possede;
use App\Entity\Commande;
use App\Form\UtilisateurType;
use App\Repository\RoleRepository;

class UtilisateurController extends AbstractController
{
    public function verifiercommandeencours(EntityManagerInterface $em, int $id): bool
    {
        $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['utilisateur_id' => $id]);

        $commandes = $em->getRepository(Commande::class)->findBy(['utilisateur_id' => $utilisateur]);

        foreach ($commandes as $commande) {
            if ($commande->getStatut() <> 'Terminé' and $commande->getStatut() <> 'Annulé') {
                return false;
            }
        }

        return true;        
    }
    #[Route('/utilisateur/liste', name: 'app_utilisateur_liste')]
    public function liste(EntityManagerInterface $em, PaginatorInterface $paginator,Request $request): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
        // On récupère tous les articles disponibles en base de données
        $query = $em->createQuery('SELECT utilisateur FROM App\Entity\Utilisateur utilisateur');

        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );          

        return $this->render('utilisateur/liste.html.twig', [
            'pagination' => $pagination
        ]);
    }    

    #[Route('/utilisateur/index/{id}', name: 'app_utilisateur_index')]
    public function index(EntityManagerInterface $em,PaginatorInterface $paginator,Request $request,int $id): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
        // On récupère l'utilisateur qui correspond à l'id passé dans l'url
        $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['utilisateur_id' => $id]);

        // On récupère tous les articles disponibles en base de données
        $queryBuilder = $em->createQueryBuilder()
            ->select('commande')
            ->from(Commande::class, 'commande')
            ->where('commande.utilisateur_id = :utilisateur')
            ->setParameter('utilisateur', $utilisateur);


        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );         

        return $this->render('utilisateur/index.html.twig', [
            'utilisateur' => $utilisateur,
            'pagination' => $pagination
        ]);
    }

    #[Route('/utilisateur/add', name: 'app_utilisateur_add')]
    public function add(EntityManagerInterface $em, Request $request, RoleRepository $roleRepository, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, MailerInterface $mailer): Response
    {
        $mode       = 'new';
        $utilisateur    = new Utilisateur();

        $form = $this->createForm(UtilisateurType::class, $utilisateur, [
            'user' => $this->getUser(), 'mode' => $mode
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $errors = $validator->validate($utilisateur);

            if (count($errors) === 0) {

                $utilisateur->setPassword(
                    $passwordHasher->hashPassword(
                        $utilisateur,
                        $form->get('password')->getData()
                    )
                );
                $roleData = $form->get('roles')->getData()[0];
                
                $role = $roleRepository->findOneBy(['libelle' => $roleData]);

                if (!$role) {
                        throw new \Exception("Role avec libelle '" . $roleData . "' not found");
                }

                $possede=new Possede();
                    
                $possede->setRoleId($role);
                $possede->setUtilisateurId($utilisateur);
                $utilisateur->addPossede($possede);
                
                $this->saveUtilisateur($utilisateur, $mode,$em);

                $email='contact@viteetgourmand.fr';

                $message = (new TemplatedEmail())
                    ->from($email)
                    ->to($utilisateur->getEmail())
                    ->subject('Bienvenue sur notre site Vite & Gourmand')
                    ->htmlTemplate('emails/bienvenue.html.twig')
                    ->context([
                        'prenom' => $utilisateur->getPrenom(),
                        'nom' => $utilisateur->getNom(),
                    ]);

                try {
                    $mailer->send($message);
                } catch (TransportExceptionInterface $e) {
                    // some error prevented the email sending; display an
                    // error message or try to resend the message
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.');
                }

                $this->addFlash('success', 'Votre compte a été créé avec succès !');


                return $this->redirectToRoute('app_utilisateur_liste');
            }
        }

        $parameters = array(
            'form'      => $form->createView(),
            'utilisateur'      => $utilisateur,
            'mode'      => $mode
        );

        return $this->render('utilisateur/edit.html.twig', $parameters);
    }

    #[Route('/utilisateur/edit/{id}', name: 'app_utilisateur_edit')]
    public function edit(EntityManagerInterface $em, Request $request, int $id=null, RoleRepository $roleRepository, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): Response
    {
        $mode = 'update';
        // On récupère l'utilisateur qui correspond à l'id passé dans l'url
        $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['utilisateur_id' => $id]);

        $form = $this->createForm(UtilisateurType::class, $utilisateur, [
            'user' => $this->getUser(), 'mode' => $mode
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) { 

            $errors = $validator->validate($utilisateur);

            if (count($errors) === 0) {        
                $utilisateur->setPassword(
                    $passwordHasher->hashPassword(
                        $utilisateur,
                        $form->get('password')->getData()
                    )
                );   
                
                $utilisateur->removePossede($utilisateur->getPossede());

                $em->flush();

                $roleData = $form->get('roles')->getData()[0];

                $role = $roleRepository->findOneBy(['libelle' => $roleData]);

                if (!$role) {
                    throw new \Exception("Role avec libelle '" . $roleData . "' not found");
                }
                
                $possede=new Possede();                

                $possede->setRoleId($role);
                $possede->setUtilisateurId($utilisateur);
                $utilisateur->addPossede($possede);
                                
                $this->saveUtilisateur($utilisateur, $mode,$em);

                return $this->redirectToRoute('app_utilisateur_liste');
            }
        }

        $parameters = array(
            'form'      => $form->createView(),
            'utilisateur'      => $utilisateur,
            'mode'      => $mode
        );

        return $this->render('utilisateur/edit.html.twig', $parameters);
    }

    #[Route('/utilisateur/remove/{id}', name: 'app_utilisateur_remove')]
    public function remove(EntityManagerInterface $em, int $id, Request $request): Response
    {
        // On récupère l'utilisateur qui correspond à l'id passé dans l'URL
        $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['utilisateur_id' => $id]);

        if (!$this->verifiercommandeencours($em, $id)) {
            $this->addFlash('danger', 'L\'utilisateur ne peut pas être supprimé car il a des commandes en cours');
            return $this->redirectToRoute('app_utilisateur_liste');
        }

        $token = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $utilisateur->getUtilisateurId(), $request->request->get('_token'))) {
            // L'utilisateur est supprimé
            $em->remove($utilisateur);
            $em->flush();
            
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès');
        } else {
            $this->addFlash('danger', 'Le token CSRF est invalide. L\'utilisateur n\'a pas été supprimé.');
        }

        return $this->redirectToRoute('app_utilisateur_liste');
    }

    /**
     * Enregistrer un utilisateur en base de données
     * 
     * @param   utilisateur     $utilisateur
     * @param   string      $mode 
     */
    private function saveUtilisateur(Utilisateur $utilisateur, string $mode, EntityManagerInterface $em){
        $em->persist($utilisateur);
        $em->flush();
        if($mode == 'new') {
            $this->addFlash('success', 'Utilisateur créé avec succès');
        } else {
            $this->addFlash('success', 'Utilisateur mis à jour avec succès');
        }
    }    
}
