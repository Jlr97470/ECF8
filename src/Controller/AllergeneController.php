<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Allergene;
use App\Entity\Plat;
use App\Entity\Contient;
use App\Form\AllergeneType;
class AllergeneController extends AbstractController
{
    #[Route('/allergene/liste', name: 'app_allergene_liste')]
    public function liste(EntityManagerInterface $em, PaginatorInterface $paginator,Request $request): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
        // On récupère tous les articles disponibles en base de données
        $query = $em->createQuery('SELECT allergene FROM App\Entity\Allergene allergene');

        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
            
        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );          

        return $this->render('allergene/liste.html.twig', [
            'pagination' => $pagination
        ]);
    }    

    #[Route('/allergene/index/{id}', name: 'app_allergene_index')]
    public function index(EntityManagerInterface $em,PaginatorInterface $paginator, Request $request,int $id): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
        // On récupère l'Allergene qui correspond à l'id passé dans l'url
        $allergene = $em->getRepository(Allergene::class)->findOneBy(['allergene_id' => $id]);

        if (!$allergene) {
            $this->addFlash('danger', 'L"Allergene n"existe pas');
            return $this->redirectToRoute('app_allergene_liste');
        }

        $plats = $allergene->getContients()->map(function($plat) {
            return $plat->getPlatId();
        })->toArray();

        if ($plats){
            // On récupère tous les articles disponibles en base de données
            $queryBuilder = $em->createQueryBuilder()
                ->select('plat')
                ->from(Plat::class, 'plat')
                ->where('plat.plat_id NOT IN (:plats)')
                ->setParameter('plats', $plats);
        }
        else
        {
            // On récupère tous les articles disponibles en base de données
            $queryBuilder = $em->createQueryBuilder()
                ->select('plat')
                ->from(Plat::class, 'plat');
        }

        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );          

        return $this->render('allergene/index.html.twig', [
            'allergene' => $allergene,
            'plats' => $plats,
            'pagination' => $pagination
        ]);
    }

    #[Route('/allergene/add', name: 'app_allergene_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $mode       = 'new';
        $allergene    = new Allergene();

        $form = $this->createForm(AllergeneType::class, $allergene);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->saveAllergene($allergene, $mode,$em);

            return $this->redirectToRoute('app_allergene_index', ['id' => $allergene->getAllergeneId()]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'allergene'      => $allergene,
            'mode'      => $mode
        );

        return $this->render('allergene/edit.html.twig', $parameters);
    }

    #[Route('/allergene/edit/{id}', name: 'app_allergene_edit')]
    public function edit(EntityManagerInterface $em, Request $request, int $id=null): Response
    {
        $mode = 'update';
        // On récupère l'Allergene qui correspond à l'id passé dans l'url
        $allergene = $em->getRepository(Allergene::class)->findOneBy(['allergene_id' => $id]);

        if (!$allergene) {
            $this->addFlash('danger', 'L"Allergene n"existe pas');
            return $this->redirectToRoute('app_allergene_liste');
        }

        $form = $this->createForm(AllergeneType::class, $allergene);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
           
            $this->saveAllergene($allergene, $mode,$em);

            return $this->redirectToRoute('app_allergene_index', ['id' => $id]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'allergene'      => $allergene,
            'mode'      => $mode
        );

        return $this->render('allergene/edit.html.twig', $parameters);
    }

    #[Route('/allergene/remove/{id}', name: 'app_allergene_remove')]
    public function remove(EntityManagerInterface $em, int $id, Request $request): Response
    {
        // On récupère l'Allergene qui correspond à l'id passé dans l'URL
        $allergene = $em->getRepository(Allergene::class)->findOneBy(['allergene_id' => $id]);

        if (!$allergene) {
            $this->addFlash('danger', 'L"Allergene n"existe pas');
            return $this->redirectToRoute('app_allergene_liste');
        }

        if ($this->isCsrfTokenValid('delete' . $id, $request->request->get('_token'))) {
            // L'Allergene est supprimé
            $em->remove($allergene);
            $em->flush();
            $this->addFlash('success', 'L"Allergene a été supprimé avec succès');
        }
        else {
            $this->addFlash('danger', 'Le token CSRF est invalide. L"Allergene n"a pas été supprimé');
        }

        return $this->redirectToRoute('app_allergene_liste');
    }

    #[Route('/allergene/platadd/{idallergene}/{idplat}', name: 'app_allergene_platadd')]
    public function platadd(EntityManagerInterface $em, int $idallergene, int $idplat): Response
    {
        // On récupère l'Allergene qui correspond à l'id passé dans l'url
        $allergene = $em->getRepository(Allergene::class)->findOneBy(['allergene_id' => $idallergene]);

        if (!$allergene) {
            $this->addFlash('danger', 'L"Allergene n"existe pas');
            return $this->redirectToRoute('app_allergene_liste');
        }

        $plat=  $em->getRepository(Plat::class)->findOneBy(['plat_id' => $idplat]);

        if (!$plat) {
            $this->addFlash('danger', 'Le plat n"existe pas');
            return $this->redirectToRoute('app_allergene_index', ['id' => $idallergene]);
        }

        $contient = new Contient();
        $contient->setAllergeneId($allergene);
        $contient->setPlatId($plat);
        $em->persist($contient);
        $em->flush();

        $this->addFlash('success', 'Le plat a été ajouté avec succès');

        return $this->redirectToRoute('app_allergene_index', ['id' => $idallergene]);
    }   
    
    #[Route('/allergene/platremove/{idallergene}/{idplat}', name: 'app_allergene_platremove')]
    public function platremove(EntityManagerInterface $em, int $idallergene, int $idplat, Request $request): Response
    {
        // On récupère l'Allergene qui correspond à l'id passé dans l'url
        $allergene = $em->getRepository(Allergene::class)->findOneBy(['allergene_id' => $idallergene]);

        if (!$allergene) {
            $this->addFlash('danger', 'L"Allergene n"existe pas');
            return $this->redirectToRoute('app_allergene_liste');
        }

        $contient=  $allergene->getContients()->filter(function($contient) use ($idplat) {
            return $contient->getPlatId()->getPlatId() === $idplat;
        })->first();

        if ($contient) {
            if ($this->isCsrfTokenValid('delete' . $contient->getPlatId()->getPlatId(), $request->request->get('_token'))) {
                $em->remove($contient);
                $em->flush();
            } else {
                $this->addFlash('danger', 'Le token CSRF est invalide. Le plat n"a pas été supprimé.');
            }
        } else {
            $this->addFlash('danger', 'Le plat n"est pas associé à cet allergène.');
        }   

        return $this->redirectToRoute('app_allergene_index', ['id' => $idallergene]);
    }     

    /**
     * Enregistrer un Allergene en base de données
     * 
     * @param   Allergene     $allergene
     * @param   string      $mode 
     */
    private function saveAllergene(Allergene $allergene, string $mode, EntityManagerInterface $em){

        $em->persist($allergene);
        $em->flush();
        if($mode == 'new') {
            $this->addFlash('success', 'L"Allergene a été ajouté avec succès');
        } else {
            $this->addFlash('success', 'L"Allergene a été modifié avec succès');
        }
    }    

}
