<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Regime;
use App\Entity\Menu;
use App\Entity\Adapte;
use App\Form\RegimeType;
class RegimeController extends AbstractController
{
    #[Route('/regime/liste', name: 'app_regime_liste')]
    public function liste(EntityManagerInterface $em, PaginatorInterface $paginator,Request $request): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
        // On récupère tous les articles disponibles en base de données
        $query = $em->createQuery('SELECT regime FROM App\Entity\Regime regime');
            
        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );          

        return $this->render('regime/liste.html.twig', [
            'pagination' => $pagination
        ]);
    }    

    #[Route('/regime/index/{id}', name: 'app_regime_index')]
    public function index(EntityManagerInterface $em,PaginatorInterface $paginator, Request $request,int $id): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
        // On récupère l'regime qui correspond à l'id passé dans l'url
        $regime = $em->getRepository(Regime::class)->findOneBy(['regime_id' => $id]);

        if (!$regime) {
            $this->addFlash('danger', 'Le regime n"existe pas');
            return $this->redirectToRoute('app_regime_liste');
        }

        $menus = $regime->getAdaptes()->map(function($adapte) {
            return $adapte->getMenuId();
        })->toArray();

        if ($menus){
            // On récupère tous les articles disponibles en base de données
            $queryBuilder = $em->createQueryBuilder()
                ->select('menu')
                ->from(Menu::class, 'menu')
                ->where('menu.menu_id NOT IN (:menus)')
                ->setParameter('menus', $menus);
        }
        else
        {
            // On récupère tous les articles disponibles en base de données
            $queryBuilder = $em->createQueryBuilder()
                ->select('menu')
                ->from(Menu::class, 'menu');
        }

        $prixMin = $request->query->get('prix_min');
        $prixMax = $request->query->get('prix_max');

        if ($prixMin !== null) {
            $queryBuilder->andWhere('menu.prix_par_personne >= :prixMin')
                ->setParameter('prixMin', $prixMin);
        }

        if ($prixMax !== null) {
            $queryBuilder->andWhere('menu.prix_par_personne <= :prixMax')
                ->setParameter('prixMax', $prixMax);
        }          

        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
        $query, /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );          

        return $this->render('regime/index.html.twig', [
            'regime' => $regime,
            'menus' => $menus,
            'pagination' => $pagination,
            'page' => $request->query->getInt('page', 1),
            'sort'=> $request->query->get('sort', ''),
            'direction'=> $request->query->get('direction', ''),
            'filterField'=> $request->query->get('filterField', ''),
            'filterValue'=> $request->query->get('filterValue', ''),
            'prix_min' => $prixMin,
            'prix_max' => $prixMax  
        ]);
    }

    #[Route('/regime/add', name: 'app_regime_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $mode       = 'new';
        $regime    = new Regime();

        $form = $this->createForm(RegimeType::class, $regime);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->saveregime($regime, $mode,$em);

            return $this->redirectToRoute('app_regime_index', ['id' => $regime->getRegimeId()]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'regime'      => $regime,
            'mode'      => $mode
        );

        return $this->render('regime/edit.html.twig', $parameters);
    }

    #[Route('/regime/edit/{id}', name: 'app_regime_edit')]
    public function edit(EntityManagerInterface $em, Request $request, int $id=null): Response
    {
        $mode = 'update';
        // On récupère l'regime qui correspond à l'id passé dans l'url
        $regime = $em->getRepository(regime::class)->findOneBy(['regime_id' => $id]);

        if (!$regime) {
            $this->addFlash('danger', 'Le regime n"existe pas');
            return $this->redirectToRoute('app_regime_liste');
        }

        $form = $this->createForm(RegimeType::class, $regime);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
           
            $this->saveregime($regime, $mode,$em);

            return $this->redirectToRoute('app_regime_index', ['id' => $id]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'regime'      => $regime,
            'mode'      => $mode
        );

        return $this->render('regime/edit.html.twig', $parameters);
    }

    #[Route('/regime/remove/{id}', name: 'app_regime_remove')]
    public function remove(EntityManagerInterface $em, Request $request, int $id): Response
    {
        // On récupère l'regime qui correspond à l'id passé dans l'URL
        $regime = $em->getRepository(regime::class)->findOneBy(['regime_id' => $id]);

        if (!$regime) {
            $this->addFlash('danger', 'L"regime n"existe pas');
            return $this->redirectToRoute('app_regime_liste');
        }
        if ($this->isCsrfTokenValid('delete' . $regime->getRegimeId(), $request->request->get('_token'))) {
            // L'regime est supprimé
            $em->remove($regime);
            $em->flush();

            $this->addFlash('success', 'L"regime a été supprimé avec succès');
        } else {
            $this->addFlash('danger', 'Le token CSRF est invalide. L"regime n"a pas été supprimé.');
        }
     
        return $this->redirectToRoute('app_regime_liste');
    }
    #[Route('/regime/menuadd/{idregime}/{idmenu}', name: 'app_regime_menuadd')]
    public function menuadd(EntityManagerInterface $em, int $idregime, int $idmenu): Response
    {
        // On récupère le regime qui correspond à l'id passé dans l'url
        $regime = $em->getRepository(regime::class)->findOneBy(['regime_id' => $idregime]);

        if (!$regime) {
            $this->addFlash('danger', 'Le regime n"existe pas');
            return $this->redirectToRoute('app_regime_liste');
        }

        $menu=  $em->getRepository(Menu::class)->findOneBy(['menu_id' => $idmenu]);

        if (!$menu) {
            $this->addFlash('danger', 'Le menu n"existe pas');
            return $this->redirectToRoute('app_regime_index', ['id' => $idregime]);
        }

        $adapte=  $menu->getAdapte();

        if ($adapte) {
            $adapte->setregimeId($regime);
            $em->persist($adapte);
            $em->flush();
        }
        else
        {
            $adapte = new Adapte();
            $adapte->setMenuId($menu);
            $adapte->setregimeId($regime);
            $regime->addAdapte($adapte);
            $em->persist($regime);
            $em->flush();
        }

        $this->addFlash('success', 'Le menu a été ajouté avec succès');

        return $this->redirectToRoute('app_regime_index', ['id' => $idregime]);
    }   
    
    #[Route('/regime/menuremove/{idregime}/{idmenu}', name: 'app_regime_menuremove')]
    public function menuremove(EntityManagerInterface $em, Request $request, int $idregime, int $idmenu): Response
    {
        // On récupère le regime qui correspond à l'id passé dans l'url
        $regime = $em->getRepository(Regime::class)->findOneBy(['regime_id' => $idregime]);

        if (!$regime) {
            $this->addFlash('danger', 'Le regime n"existe pas');
            return $this->redirectToRoute('app_regime_liste');
        }

        $menu=  $em->getRepository(Menu::class)->findOneBy(['menu_id' => $idmenu]);

        if (!$menu) {
            $this->addFlash('danger', 'Le menu n"existe pas');
            return $this->redirectToRoute('app_regime_index', ['id' => $idregime]);
        }

        $adapte=  $regime->getAdaptes()->filter(function($adapte) use ($idmenu) {
            return $adapte->getMenuId()->getMenuId() === $idmenu;
        })->first();

        if ($adapte) {
            if ($this->isCsrfTokenValid('delete' . $adapte->getMenuId()->getMenuId(), $request->request->get('_token'))) {
                $em->remove($adapte);
                $em->flush();
                $this->addFlash('success', 'Le menu a été supprimé avec succès');
            } else {
                $this->addFlash('danger', 'Le token CSRF est invalide. Le menu n"a pas été supprimé.');
            }
        } else {
            $this->addFlash('danger', 'Le menu n"est pas associé à ce régime.');
        }

        return $this->redirectToRoute('app_regime_index', ['id' => $idregime]);
    }     

    /**
     * Enregistrer un regime en base de données
     * 
     * @param   Regime     $regime
     * @param   string      $mode 
     */
    private function saveregime(Regime $regime, string $mode, EntityManagerInterface $em){

        $em->persist($regime);
        $em->flush();
        if($mode == 'new') {
            $this->addFlash('success', 'Le regime a été ajouté avec succès');
        } else {
            $this->addFlash('success', 'Le regime a été modifié avec succès');
        }
    }    

}

