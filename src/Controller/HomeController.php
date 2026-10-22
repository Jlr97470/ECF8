<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use App\Entity\Avis;
use App\Entity\Horaire;
use App\Entity\Role;
use App\Entity\Contact;
use App\Form\ContactType;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(EntityManagerInterface $em): Response
    {
        // On récupère tous les articles disponibles en base de données
        $avis = $em->getRepository(Avis::class)->findBy(['statut' => 'validé'], orderBy: ['avis_id' => 'DESC'], limit: 6);

        return $this->render('home/index.html.twig', [
            'avis' => $avis
        ]);
    }

    #[Route('/homepage/conditiongeneralcgv', name: 'homepage_conditiongeneralcgv')]
    public function conditionGeneralcgv(): Response
    {
        // On récupère tous les articles disponibles en base de données
        return $this->render('home/conditiongeneralcgv.html.twig');
    }

    #[Route('/homepage/conditiongeneralcgu', name: 'homepage_conditiongeneralcgu')]
    public function conditionGeneralcgu(): Response
    {
        // On récupère tous les articles disponibles en base de données
        return $this->render('home/conditiongeneralcgu.html.twig');
    }

    #[Route('/homepage/mentionlegal', name: 'homepage_mentionlegal')]
    public function mentionLegal(): Response
    {
        // On récupère tous les articles disponibles en base de données
        return $this->render('home/mentionlegal.html.twig');
    }
    
    #[Route('/homepage/contact', name: 'homepage_contact')]
    public function contact(Request $request, MailerInterface $mailer,EntityManagerInterface $em): Response
    {
        $contact= new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
 
        $role = $em->getRepository(Role::class)->findOneBy(['libelle' => 'ROLE_ADMIN']);

        $possedes= $role->getPossedes();

        if ($form->isSubmitted() && $form->isValid()) {
 
            $prenom = $contact->getPrenom();
            $nom = $contact->getNom();
            $getmail = $contact->getEmail();
            $gettitre = $contact->getTitre();
            $getmessage = $contact->getMessage();

            foreach ($possedes as $possede) {

                $user =  $possede->getUtilisateurId();

                $message = (new TemplatedEmail())
                    ->from($getmail)
                    ->to($user->getEmail())
                    ->subject('Demande de contact')
                    ->htmlTemplate('emails/contact.html.twig')
                    ->context([
                        'prenom' => $prenom,
                        'nom' => $nom,
                        'titre' => $gettitre,
                        'message' => $getmessage,
                        'mail' => $getmail,
                    ]);
                try {
                    $mailer->send($message);
                } catch (TransportExceptionInterface $e) {
                    // some error prevented the email sending; display an
                    // error message or try to resend the message
                    $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer plus tard.');
                    
                    return $this->redirectToRoute('homepage_contact');
                }
            }

            $this->addFlash('success', 'Votre message a été envoyé');
 
            return $this->redirectToRoute('homepage_contact');
        }
 
        return $this->render('home/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/homepage/horaire', name: 'homepage_horaire')]
    public function horaire(EntityManagerInterface $em): Response
    {
        // On récupère l'plat qui correspond à l'id passé dans l'url
        $horaires = $em->getRepository(Horaire::class)->findAll();

        $tableau = array_map(fn (Horaire $horaire) => $horaire->getJour().': '.$horaire->getHeureOuverture().': '.$horaire->getHeureFermeture(), $horaires);

        $html = '<ul>';

        foreach ($tableau as $ligne) {
            $html .= '<li>'.$ligne.'</li>';
        }
        $html .= '</ul>';

        return new Response($html, 200, ['Content-Type' => 'text/html']);
    }

}
