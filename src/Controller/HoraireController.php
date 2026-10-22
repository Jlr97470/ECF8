<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Horaire;
use App\Form\HoraireType;
class HoraireController extends AbstractController
{
    #[Route('/horaire/liste', name: 'app_horaire_liste')]
    public function liste(EntityManagerInterface $em, PaginatorInterface $paginator,Request $request): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue')) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }

        // On récupère tous les articles disponibles en base de données
        $query = $em->createQuery('SELECT horaire FROM App\Entity\Horaire horaire');
            
        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );          

        return $this->render('horaire/liste.html.twig', [
            'pagination' => $pagination
        ]);
    }    

    #[Route('/horaire/index/{id}', name: 'app_horaire_index')]
    public function index(EntityManagerInterface $em,int $id): Response
    {
        // On récupère l'Allergene qui correspond à l'id passé dans l'url
        $horaire = $em->getRepository(Horaire::class)->findOneBy(['horaire_id' => $id]);

        if (!$horaire) {
            $this->addFlash('danger', 'L"Horaire n"existe pas');
            return $this->redirectToRoute('app_horaire_liste');
        }

        return $this->render('horaire/index.html.twig', [
            'horaire' => $horaire,
        ]);
    }

    #[Route('/horaire/add', name: 'app_horaire_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $mode       = 'new';
        $horaire    = new Horaire();

        $form = $this->createForm(HoraireType::class, $horaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->saveHoraire($horaire, $mode,$em);

            return $this->redirectToRoute('app_horaire_index', ['id' => $horaire->getHoraireId()]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'allergene'      => $horaire,
            'mode'      => $mode
        );

        return $this->render('horaire/edit.html.twig', $parameters);
    }

    #[Route('/horaire/edit/{id}', name: 'app_horaire_edit')]
    public function edit(EntityManagerInterface $em, Request $request, int $id=null): Response
    {
        $mode = 'update';
        // On récupère l'Allergene qui correspond à l'id passé dans l'url
        $horaire = $em->getRepository(Horaire::class)->findOneBy(['horaire_id' => $id]);

        if (!$horaire) {
            $this->addFlash('danger', 'L"Horaire n"existe pas');
            return $this->redirectToRoute('app_horaire_liste');
        }

        $form = $this->createForm(HoraireType::class, $horaire);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
           
            $this->saveHoraire($horaire, $mode,$em);

            return $this->redirectToRoute('app_horaire_index', ['id' => $id]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'horaire'      => $horaire,
            'mode'      => $mode
        );

        return $this->render('horaire/edit.html.twig', $parameters);
    }

    #[Route('/horaire/remove/{id}', name: 'app_horaire_remove')]
    public function remove(EntityManagerInterface $em, Request $request, int $id): Response
    {
        // On récupère l'Allergene qui correspond à l'id passé dans l'URL
        $horaire = $em->getRepository(Horaire::class)->findOneBy(['horaire_id' => $id]);

        if (!$horaire) {
            $this->addFlash('danger', 'L"Horaire n"existe pas');
            return $this->redirectToRoute('app_horaire_liste');
        }

        if ($this->isCsrfTokenValid('delete' . $horaire->getHoraireId(), $request->request->get('_token'))) {
            // L'Allergene est supprimé
            $em->remove($horaire);
            $em->flush();

            $this->addFlash('success', 'L"Horaire a été supprimé avec succès');
        } else {
            $this->addFlash('danger', 'Le token CSRF est invalide. L"Horaire n"a pas été supprimé.');
        }

        return $this->redirectToRoute('app_horaire_liste');
    }

    /**
     * Enregistrer un horaire en base de données
     * 
     * @param   horaire     $horaire
     * @param   string      $mode 
     */
    private function saveHoraire(Horaire $horaire, string $mode, EntityManagerInterface $em){

        $em->persist($horaire);
        $em->flush();
        if($mode == 'new') {
            $this->addFlash('success', 'L"Horaire a été ajouté avec succès');
        } else {
            $this->addFlash('success', 'L"Horaire a été modifié avec succès');
        }
    }    

}
