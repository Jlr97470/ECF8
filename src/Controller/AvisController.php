<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Avis;
use App\Entity\Publie;
use App\Form\AvisType;
use Symfony\Component\Security\Csrf\CsrfToken;

class AvisController extends AbstractController
{
    #[Route('/avis/liste', name: 'app_avis_liste')]
    public function liste(EntityManagerInterface $em, PaginatorInterface $paginator,Request $request): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue')) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
        // On récupère tous les articles disponibles en base de données
        $user = $this->getUser();
        
        if ($user && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_USE', $user->getRoles())))
            {
                $query = $em->createQuery('SELECT avis FROM App\Entity\Avis avis');
            }
        else
            {
                $publie = $user->getPublie();
                if ($publie) {
                    $query = $em->createQuery('SELECT avis FROM App\Entity\Avis avis WHERE avis.avis_id = :avisId')
                        ->setParameter('avisId', $publie->getAvisId());
                } else {
                    $query = $em->createQuery('SELECT avis FROM App\Entity\Avis avis WHERE 1=0');
                }
            }
            
        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );          

        return $this->render('avis/liste.html.twig', [
            'pagination' => $pagination
        ]);
    }    

    #[Route('/avis/index/{id}', name: 'app_avis_index')]
    public function index(EntityManagerInterface $em,int $id): Response
    {
        // On récupère l'Avis qui correspond à l'id passé dans l'url
        $avis = $em->getRepository(Avis::class)->findOneBy(['avis_id' => $id]);

        if (!$avis) {
            $this->addFlash('danger', 'L"Avis n"existe pas');
            return $this->redirectToRoute('app_avis_liste');
        }
      
        return $this->render('avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    #[Route('/avis/add', name: 'app_avis_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $mode       = 'new';
        $avis    = new Avis();

        $form = $this->createForm(AvisType::class, $avis, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $maxId = $em->getRepository(Avis::class)->createQueryBuilder('b')
            ->select("MAX(b.avis_id) as maxId")
            ->getQuery()
            ->getSingleResult();            

            $avis->setAvisId($maxId['maxId'] + 1);

            $avis->setStatut('En attente de validation');

            $this->saveAvis($avis, $mode,$em);

            $user = $this->getUser();

            $publie = $user->getPublie();

            if ($publie) {
                $publie->setAvisId($avis);
                $em->persist($publie);
                $em->flush();
            }
            else
            {
                $publie = new Publie();
                $publie->setUtilisateurId($user);
                $publie->setAvisId($avis);
                $user->addPublie($publie);
                $em->persist($user);
                $em->flush();
            }            

            return $this->redirectToRoute('app_avis_index', ['id' => $avis->getAvisId()]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'avis'      => $avis,
            'mode'      => $mode
        );

        return $this->render('avis/edit.html.twig', $parameters);
    }

    #[Route('/avis/edit/{id}', name: 'app_avis_edit')]
    public function edit(EntityManagerInterface $em, Request $request, int $id=null): Response
    {
        $mode = 'update';
        // On récupère l'Avis qui correspond à l'id passé dans l'url
        $avis = $em->getRepository(Avis::class)->findOneBy(['avis_id' => $id]);

        if (!$avis) {
            $this->addFlash('danger', 'L"Avis n"existe pas');
            return $this->redirectToRoute('app_avis_liste');
        }

        $form = $this->createForm(AvisType::class, $avis, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
           
            $this->saveAvis($avis, $mode,$em);

            return $this->redirectToRoute('app_avis_index', ['id' => $id]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'avis'      => $avis,
            'mode'      => $mode
        );

        return $this->render('avis/edit.html.twig', $parameters);
    }

    #[Route('/avis/remove/{id}', name: 'app_avis_remove')]
    public function remove(EntityManagerInterface $em, Request $request, int $id): Response
    {
        // On récupère l'Avis qui correspond à l'id passé dans l'URL
        $avis = $em->getRepository(Avis::class)->findOneBy(['avis_id' => $id]);

        if (!$avis) {
            $this->addFlash('danger', 'L"Avis n"existe pas');
            return $this->redirectToRoute('app_avis_liste');
        }

        if ($this->isCsrfTokenValid('delete' . $avis->getAvisId(), $request->request->get('_token'))) {
            $em->remove($avis);
            $em->flush();

            $this->addFlash('success', 'L"Avis a été supprimé avec succès');
        } else {
            $this->addFlash('danger', 'Jeton CSRF invalide. L"Avis n"a pas été supprimé.');
        }

        return $this->redirectToRoute('app_avis_liste');
    }

    /**
     * Enregistrer un Avis en base de données
     * 
     * @param   Avis     $avis
     * @param   string      $mode 
     */
    private function saveAvis(Avis $avis, string $mode, EntityManagerInterface $em){

        $em->persist($avis);
        $em->flush();
        if($mode == 'new') {
            $this->addFlash('success', 'L"Avis a été ajouté avec succès');
        } else {
            $this->addFlash('success', 'L"Avis a été modifié avec succès');
        }
    }    

}
