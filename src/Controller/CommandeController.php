<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use App\Entity\Commande;
use App\Entity\Menu;
use App\Entity\Horaire;
use App\Entity\Utilisateur;
use App\Form\CommandeType;
use Symfony\Component\Security\Csrf\CsrfToken;

class CommandeController extends AbstractController
{
    public function verifierHeurePrestation(EntityManagerInterface $em,Commande $commande): bool
    {
        $jours= array("Dimanche", "Lundi", "Mardi",
                  "Mercredi", "Jeudi", "Vendredi",
                  "Samedi");

        $JourPrestation =  $jours[Date('w', $commande->getDatePrestation()->getTimestamp())];

        $heureLivraison = $commande->getHeureLivraison();

        $queryBuilder = $em->createQueryBuilder()
                ->select('horaire')
                ->from(Horaire::class, 'horaire')
                ->where('horaire.jour = :jour')
                ->andWhere('horaire.heure_ouverture <= :heureLivraison')
                ->andWhere('horaire.heure_fermeture >= :heureLivraison')
                ->setParameter('jour', $JourPrestation)
                ->setParameter('heureLivraison', $heureLivraison);

        $horaire = $queryBuilder->getQuery()->getOneOrNullResult();

        if (!$horaire) {
            return false;
        }

        return true;        
    }

    #[Route('/commande/liste', name: 'app_commande_liste')]
    public function liste(EntityManagerInterface $em, PaginatorInterface $paginator,Request $request): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        } 
        // On récupère tous les articles disponibles en base de données
        $user = $this->getUser();
        
        if ($user && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_USE', $user->getRoles())))
        {
            // On récupère tous les articles disponibles en base de données
            $queryBuilder = $em->createQueryBuilder()
                ->select('commande')
                ->from(Commande::class, 'commande');            
        }
        else
        {
            // On récupère tous les articles disponibles en base de données
            $queryBuilder = $em->createQueryBuilder()
                ->select('commande')
                ->from(Commande::class, 'commande')
                ->where('commande.utilisateur_id IN (:user)')
                ->setParameter('user', $user);
        }

        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );      

        return $this->render('commande/liste.html.twig', [
            'pagination' => $pagination
        ]);
    }    

    #[Route('/commande/index/{id}', name: 'app_commande_index')]
    public function index(EntityManagerInterface $em, Request $request, string $id, PaginatorInterface $paginator): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
        // On récupère l'commande qui correspond à l'id passé dans l'url
        $commande = $em->getRepository(Commande::class)->findOneBy(['numero_commande' => $id]);

        if (!$commande) {
            $this->addFlash('danger', 'Le commande n\'existe pas');

            return $this->redirectToRoute('app_commande_liste');
        }

        $menu = $commande->getMenuId();

        if ($menu){
            // On récupère tous les articles disponibles en base de données
            $queryBuilder = $em->createQueryBuilder()
                ->select('menu')
                ->from(Menu::class, 'menu')
                ->where('menu.menu_id NOT IN (:menu)')
                ->setParameter('menu', $menu);
        }
        else
        {
            // On récupère tous les articles disponibles en base de données
            $queryBuilder = $em->createQueryBuilder()
                ->select('menu')
                ->from(Menu::class, 'menu');
        }

        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        

        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );           

        return $this->render('commande/index.html.twig', [
            'commande' => $commande,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/commande/add/{idmenu}', name: 'app_commande_add')]
    public function add(EntityManagerInterface $em, Request $request, MailerInterface $mailer, int $idmenu): Response
    {
        $mode       = 'new';
        $commande    = new Commande();

        $menu = $em->getRepository(Menu::class)->findOneBy(['menu_id' => $idmenu]);

        if ($menu){
            $commande->setMenuId($menu);
        }
        else
        {
            $this->addFlash('danger', 'Le menu n\'existe pas');      
            return $this->redirectToRoute('app_commande_liste');
        }

        $user = $this->getUser();
    
        if ($user && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_USE', $user->getRoles())))
        {
            return $this->redirectToRoute('app_commande_liste');
        }
        else
        {
            $commande->setUtilisateurId($user);
        }

        $commande->setNumeroCommande(uniqid('CMD-'));
        $commande->setDateCommande(new \DateTime());
        $commande->setStatut('En cours');
        $commande->setPretmateriel(false);
        $commande->setRestitutionmateriel(false);

        $form = $this->createForm(CommandeType::class, $commande, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if ($menu){

                if (!$this->verifierHeurePrestation($em, $commande)) {
                    $this->addFlash('danger', 'La date de prestation doit être comprise dans les horaires d\'ouverture du service de livraison');

                    $parameters = array(
                        'form'      => $form->createView(),
                        'commande'      => $commande,
                        'mode'      => $mode
                    );

                    return $this->render('commande/edit.html.twig', $parameters);
                }

                if ($commande->getDateCommande() > $commande->getDatePrestation()) {
                    $this->addFlash('danger', 'La date de livraison doit être supérieure à la date de commande');

                    $parameters = array(
                        'form'      => $form->createView(),
                        'commande'      => $commande,
                        'mode'      => $mode
                    );

                    return $this->render('commande/edit.html.twig', $parameters);
                }            

                if ($menu->getNombrePersonneMinimum() > $commande->getNombrepersonne()) {
                    $this->addFlash('danger', 'Le nombre de personne doit être supérieur ou égal à '.$menu->getNombrePersonneMinimum());
                    $parameters = array(
                        'form'      => $form->createView(),
                        'commande'      => $commande,
                        'mode'      => $mode
                    );

                    return $this->render('commande/edit.html.twig', $parameters);                }

                if ($menu->getQuantiteRestante() < $commande->getNombrepersonne()) {
                    $this->addFlash('danger', 'Le nombre de personne doit être inférieur ou égal à '.$menu->getQuantiteRestante());
                    $parameters = array(
                        'form'      => $form->createView(),
                        'commande'      => $commande,
                        'mode'      => $mode
                    );

                    return $this->render('commande/edit.html.twig', $parameters);
                }

                if ($commande->getMenuId()->getNombrePersonneMinimum() + 5 <= $commande->getNombrepersonne()) {
                    $commande->setPrixMenu($commande->getMenuId()->getPrixParPersonne()*$commande->getNombrePersonne()*0.9);
                    $commande->setPrixlivraison(5);  
                }
                else {  
                    $commande->setPrixMenu($commande->getMenuId()->getPrixParPersonne()*$commande->getNombrePersonne());
                    $commande->setPrixlivraison(5);  
                } 

                $menu = $commande->getMenuId();
                $menu->setQuantiteRestante($menu->getQuantiteRestante() - $commande->getNombrepersonne());
                $em->persist($menu);                
            }

            $this->savecommande($commande, $mode, $em);

            $email='contact@viteetgourmand.fr';

            $utilisateur = $commande->getUtilisateurId();

            $message = (new TemplatedEmail())
                ->from($email)
                ->to($utilisateur->getEmail())
                ->subject('Votre commande sur notre site Vite & Gourmand')
                ->htmlTemplate('emails/commande.html.twig')
                ->context([
                    'prenom' => $utilisateur->getPrenom(),
                    'nom' => $utilisateur->getNom(),
                    'commande' => $commande
                ]);

            try {
                $mailer->send($message);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.');
            }

            return $this->redirectToRoute('app_commande_index', ['id' => $commande->getNumeroCommande()]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'commande'      => $commande,
            'mode'      => $mode
        );

        return $this->render('commande/edit.html.twig', $parameters);
    }

    #[Route('/commande/edit/{id}', name: 'app_commande_edit')]
    public function edit(EntityManagerInterface $em, Request $request, MailerInterface $mailer, string $id): Response
    {
        $mode = 'update';
        // On récupère l'commande qui correspond à l'id passé dans l'url
        $commande = $em->getRepository(Commande::class)->findOneBy(['numero_commande' => $id]);

        if (!$commande) {
            $this->addFlash('danger', 'Le commande n\'existe pas');

            return $this->redirectToRoute('app_commande_liste');
        }

        $user = $this->getUser();

        if ($user && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_USE', $user->getRoles())))
        {
        }
        else
        {
            if ($commande->getUtilisateurId() && $commande->getUtilisateurId()->getUtilisateurId() !== $this->getUser()->getUtilisateurId()) {
                $this->addFlash('danger', 'Vous n\'êtes pas autorisé à modifier cette commande');
                return $this->redirectToRoute('app_commande_index', ['id' => $id]);
            }

            if ($commande->getStatut() <> 'En cours') {
                $this->addFlash('danger', 'Le commande ne peut pas être modifié car il déjà en cours de livraison ou livré');   
                return $this->redirectToRoute('app_commande_index', ['id' => $id]);
            }
        }

        $form = $this->createForm(CommandeType::class, $commande, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {  

            if (!$this->verifierHeurePrestation($em, $commande)) {
                $this->addFlash('danger', 'La date de prestation doit être comprise dans les horaires d\'ouverture du service de livraison');

                $parameters = array(
                    'form'      => $form->createView(),
                    'commande'      => $commande,
                    'mode'      => $mode
                );

                return $this->render('commande/edit.html.twig', $parameters);
            }

            if ($commande->getDateCommande() > $commande->getDatePrestation()) {
                $this->addFlash('danger', 'La date de livraison doit être supérieure à la date de commande');

                $parameters = array(
                    'form'      => $form->createView(),
                    'commande'      => $commande,
                    'mode'      => $mode
                );

                return $this->render('commande/edit.html.twig', $parameters);
            }

            if ($commande->getMenuId()->getNombrePersonneMinimum() > $commande->getNombrepersonne()) {
                $this->addFlash('danger', 'Le nombre de personne doit être supérieur ou égal à '.$commande->getMenuId()->getNombrePersonneMinimum());

                $parameters = array(
                    'form'      => $form->createView(),
                    'commande'      => $commande,
                    'mode'      => $mode
                );

                return $this->render('commande/edit.html.twig', $parameters);
            }
            
            if ($commande->getMenuId()->getQuantiteRestante() < $commande->getNombrepersonne()) {
                $this->addFlash('danger', 'Le nombre de personne doit être inférieur ou égal à '.$commande->getMenuId()->getQuantiteRestante());
                
                $parameters = array(
                    'form'      => $form->createView(),
                    'commande'      => $commande,
                    'mode'      => $mode
                );

                return $this->render('commande/edit.html.twig', $parameters);
            }

            if ($commande->getMenuId()->getNombrePersonneMinimum() + 5 <= $commande->getNombrepersonne()) {
                $commande->setPrixMenu($commande->getMenuId()->getPrixParPersonne()*$commande->getNombrePersonne()*0.9);
                $commande->setPrixlivraison(5);  
            }
            else {  
                $commande->setPrixMenu($commande->getMenuId()->getPrixParPersonne()*$commande->getNombrePersonne());
                $commande->setPrixlivraison(5);  
            }

            $this->savecommande($commande, $mode, $em);

            if ($commande->getStatut() == 'Terminé') {
                $email='contact@viteetgourmand.fr';

                $utilisateur = $commande->getUtilisateurId();

                $message = (new TemplatedEmail())
                    ->from($email)
                    ->to($utilisateur->getEmail())
                    ->subject('Votre commande sur notre site Vite & Gourmand')
                    ->htmlTemplate('emails/commandeterminer.html.twig')
                    ->context([
                        'prenom' => $utilisateur->getPrenom(),
                        'nom' => $utilisateur->getNom(),
                        'commande' => $commande
                    ]);

                try {
                    $mailer->send($message);
                } catch (TransportExceptionInterface $e) {
                    // some error prevented the email sending; display an
                    // error message or try to resend the message
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.');
                }
            }

            return $this->redirectToRoute('app_commande_index', ['id' => $id]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'commande'      => $commande,
            'mode'      => $mode
        );

        return $this->render('commande/edit.html.twig', $parameters);
    }

    #[Route('/commande/remove/{id}', name: 'app_commande_remove')]
    public function remove(EntityManagerInterface $em, Request $request, string $id): Response
    {
        // On récupère l'commande qui correspond à l'id passé dans l'URL
        $commande = $em->getRepository(Commande::class)->findOneBy(['numero_commande' => $id]);

        if (!$commande) {
            $this->addFlash('danger', 'Le commande n\'existe pas');

            return $this->redirectToRoute('app_commande_liste');
        }

        if ($commande->getStatut() <> 'En cours') {
            $this->addFlash('danger', 'Le commande ne peut pas être supprimé car il déjà en cours de livraison ou livré');   
            return $this->redirectToRoute('app_commande_index', ['id' => $id]);
        }

        if ($commande->getMenuId()) {
            $menu = $commande->getMenuId();
            $menu->setQuantiteRestante($menu->getQuantiteRestante() + $commande->getNombrepersonne());
            $em->persist($menu);
        }

        if ($this->isCsrfTokenValid('delete' . $commande->getNumeroCommande(), $request->request->get('_token'))) {
            // L'ID du token CSRF est valide, on peut supprimer l'article
            // L'commande est supprimé
            $em->remove($commande);
            $em->flush();
            
            $this->addFlash('success', 'Le commande a été supprimé avec succès');

        } else {
            // L'ID du token CSRF n'est pas valide, on ne supprime pas l'article
            $this->addFlash('danger', 'Le token CSRF est invalide. La commande n\'a pas été supprimée.');
        }

        return $this->redirectToRoute('app_commande_liste');
    }

    #[Route('/commande/menuadd/{idcommande}/{idmenu}', name: 'app_commande_menuadd')]
    public function menuadd(EntityManagerInterface $em, string $idcommande, int $idmenu): Response
    {
        // On récupère l'commande qui correspond à l'id passé dans l'url
        $commande = $em->getRepository(commande::class)->findOneBy(['numero_commande' => $idcommande]);

        $menu=  $em->getRepository(Menu::class)->findOneBy(['menu_id' => $idmenu]);

        if (!$commande) {
            $this->addFlash('danger', 'Le commande n\'existe pas');

            return $this->redirectToRoute('app_commande_liste');
        }

        if (!$menu) {
            $this->addFlash('danger', 'Le menu n\'existe pas');      
            return $this->redirectToRoute('app_commande_index', ['id' => $idcommande]);
        }

        if ($commande->getMenuId()) {
            $menu_old = $commande->getMenuId();
            $menu_old->setQuantiteRestante($menu_old->getQuantiteRestante() + $commande->getNombrepersonne());
            $em->persist($menu_old);
        }        

        if ($menu->getNombrePersonneMinimum() > $commande->getNombrepersonne()) {
            $this->addFlash('danger', 'Le nombre de personne doit être supérieur ou égal à '.$menu->getNombrePersonneMinimum());
            return $this->redirectToRoute('app_commande_edit', ['id' => $idcommande]);
        }
        
        if ($menu->getQuantiteRestante() < $commande->getNombrepersonne()) {
            $this->addFlash('danger', 'Le nombre de personne doit être inférieur ou égal à '.$menu->getQuantiteRestante());
            return $this->redirectToRoute('app_commande_edit', ['id' => $idcommande]);
        }        

        if ($commande->getMenuId()->getNombrePersonneMinimum() + 5 <= $commande->getNombrepersonne()) {
            $commande->setPrixMenu($commande->getMenuId()->getPrixParPersonne()*$commande->getNombrePersonne()*0.9);
            $commande->setPrixlivraison(5);  
        }
        else {  
            $commande->setPrixMenu($commande->getMenuId()->getPrixParPersonne()*$commande->getNombrePersonne());
            $commande->setPrixlivraison(5);  
        } 

        $menu->setQuantiteRestante($menu->getQuantiteRestante() - $commande->getNombrepersonne());

        $commande->setMenuId($menu);
        $em->persist($commande);
        $em->persist($menu);
        $em->flush();

        $this->addFlash('success', 'Le menu a été ajouté avec succès');

        return $this->redirectToRoute('app_commande_index', ['id' => $idcommande]);
    }    
 
    #[Route('/commande/utilisateuradd/{idcommande}/{idutilisateur}', name: 'app_commande_utilisateuradd')]
    public function utilisateuradd(EntityManagerInterface $em, string $idcommande, int $idutilisateur): Response
    {
        // On récupère l'commande qui correspond à l'id passé dans l'url
        $commande = $em->getRepository(commande::class)->findOneBy(['numero_commande' => $idcommande]);

        $user=  $em->getRepository(Utilisateur::class)->findOneBy(['utilisateur_id' => $idutilisateur]);

        if (!$commande) {
            $this->addFlash('danger', 'Le commande n\'existe pas');

            return $this->redirectToRoute('app_commande_liste');
        }

        if (!$user) {
            $this->addFlash('danger', "L'Utilisateur n'existe pas");
            return $this->redirectToRoute('app_commande_index', ['id' => $idcommande]);
        }       

        $commande->setUtilisateurId($user);
        $em->persist($commande);
        $em->flush();

        $this->addFlash('success', 'L\'Utilisateur a été ajouté avec succès');

        return $this->redirectToRoute('app_commande_index', ['id' => $idcommande]);
    }    
    
    /**
     * Enregistrer un commande en base de données
     * 
     * @param   commande     $commande
     * @param   string      $mode 
     */
    private function savecommande(commande $commande, string $mode, EntityManagerInterface $em): void{
        $em->persist($commande);
        $em->flush();
        
        if($mode == 'new') {
            $this->addFlash('success', 'La commande créé avec succès');
        } else {
            $this->addFlash('success', 'La commande mis à jour avec succès');
        }
    }    
}

