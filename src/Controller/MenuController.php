<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\ProposePlat;
use App\Entity\RechercherPrix;

use App\Form\MenuType;
use App\Form\RechercherPrixType;
class MenuController extends AbstractController
{
    #[Route('/menu/liste', name: 'app_menu_liste')]
    public function liste(EntityManagerInterface $em, PaginatorInterface $paginator,Request $request): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }          
        // On récupère tous les articles disponibles en base de données
        $queryBuilder = $em->createQueryBuilder()
            ->select('menu')
            ->from(Menu::class, 'menu');

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
      
        $pagination = $paginator->paginate(
        $queryBuilder->getQuery(), /* query NOT result */
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
        );      

        return $this->render('menu/liste.html.twig', [
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

    #[Route('/menu/index/{id}', name: 'app_menu_index')]
    public function index(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request, int $id): Response
    {
        if ($request->query->get('filterField') && $request->query->get('filterValue') && !is_numeric($request->query->get('filterValue'))) {
            $request->query->set('filterValue', "*".$request->query->get('filterValue')."*");
        }        
        // On récupère l'Menu qui correspond à l'id passé dans l'url
        $menu = $em->getRepository(Menu::class)->findOneBy(['menu_id' => $id]);

        if (!$menu) {
            $this->addFlash('danger', 'Le menu n\'existe pas');
            return $this->redirectToRoute('app_menu_liste');
        }

        $plats = $menu->getProposePlats()->map(function($proposeplat) {
            return $proposeplat->getPlatId();
        })->toArray();

        if ($menu->getProposeTheme()) {
            $theme = $menu->getProposeTheme()->getThemeId();
        }
        else {
            $theme = null;
        }
        
        if ($menu->getAdapte()) {
            $regime = $menu->getAdapte()->getRegimeId();
        }
        else {
            $regime = null;
        }

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

        return $this->render('menu/index.html.twig', [
            'theme' => $theme,
            'regime' => $regime,
            'menu' => $menu,
            'plats' => $plats,
            'pagination' => $pagination
        ]);
    }

    #[Route('/menu/add', name: 'app_menu_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $mode       = 'new';
        $menu    = new Menu();

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->saveMenu($menu, $mode, $em);

            return $this->redirectToRoute('app_menu_index', ['id' => $menu->getMenuId()]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'menu'      => $menu,
            'mode'      => $mode
        );

        return $this->render('menu/edit.html.twig', $parameters);
    }

    #[Route('/menu/edit/{id}', name: 'app_menu_edit')]
    public function edit(EntityManagerInterface $em, Request $request, int $id=null): Response
    {
        $mode = 'update';
        // On récupère l'Menu qui correspond à l'id passé dans l'url
        $menu = $em->getRepository(Menu::class)->findOneBy(['menu_id' => $id]);

        if (!$menu) {
            $this->addFlash('danger', 'Le menu n\'existe pas');
            return $this->redirectToRoute('app_menu_liste');
        }

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {            
            $this->saveMenu($menu, $mode, $em);

            return $this->redirectToRoute('app_menu_index', ['id' => $id]);
        }

        $parameters = array(
            'form'      => $form->createView(),
            'Menu'      => $menu,
            'mode'      => $mode
        );

        return $this->render('menu/edit.html.twig', $parameters);
    }

    #[Route('/menu/remove/{id}', name: 'app_menu_remove')]
    public function remove(EntityManagerInterface $em, Request $request, int $id): Response
    {
        // On récupère l'Menu qui correspond à l'id passé dans l'URL
        $menu = $em->getRepository(Menu::class)->findOneBy(['menu_id' => $id]);

        if (!$menu) {
            $this->addFlash('danger', 'Le menu n\'existe pas');
            return $this->redirectToRoute('app_menu_liste');
        }

        if ($this->isCsrfTokenValid('delete' . $menu->getMenuId(), $request->request->get('_token'))) {
            // L'Menu est supprimé
            $em->remove($menu);
            $em->flush();
            
            $this->addFlash('success', 'Le menu a été supprimé avec succès');
        } else {
            $this->addFlash('danger', 'Le token CSRF est invalide. Le menu n\'a pas été supprimé');
        }

        return $this->redirectToRoute('app_menu_liste');
    }

    #[Route('/menu/platadd/{idmenu}/{idplat}', name: 'app_menu_platadd')]
    public function platadd(EntityManagerInterface $em, int $idmenu, int $idplat): Response
    {
        // On récupère l'Menu qui correspond à l'id passé dans l'url
        $menu = $em->getRepository(Menu::class)->findOneBy(['menu_id' => $idmenu]);

        if (!$menu) {
            $this->addFlash('danger', 'Le menu n\'existe pas');
            return $this->redirectToRoute('app_menu_liste');
        }

        $plat=  $em->getRepository(Plat::class)->findOneBy(['plat_id' => $idplat]);
        
        if (!$plat) {
            $this->addFlash('danger', 'Le plat n\'existe pas');
            return $this->redirectToRoute('app_menu_index', ['id' => $idmenu]);
        }

        $proposeplat = new ProposePlat();
        $proposeplat->setMenuId($menu);
        $proposeplat->setPlatId($plat);
        $em->persist($proposeplat);
        $em->flush();

        $this->addFlash('success', 'Le plat a été ajouté avec succès');

        return $this->redirectToRoute('app_menu_index', ['id' => $idmenu]);
    }   
    
      #[Route('/menu/platremove/{idmenu}/{idplat}', name: 'app_menu_platremove')]
    public function platremove(EntityManagerInterface $em, Request $request, int $idmenu, int $idplat): Response
    {
        // On récupère l'Menu qui correspond à l'id passé dans l'url
        $menu = $em->getRepository(Menu::class)->findOneBy(['menu_id' => $idmenu]);

        if (!$menu) {
            $this->addFlash('danger', 'Le menu n\'existe pas');
            return $this->redirectToRoute('app_menu_liste');
        }

        $proposeplat=  $menu->getProposePlats()->filter(function($proposeplat) use ($idplat) {
            return $proposeplat->getPlatId()->getPlatId() === $idplat;
        })->first();

        if ($proposeplat) {
            if ($this->isCsrfTokenValid('delete' . $proposeplat->getPlatId()->getPlatId(), $request->request->get('_token'))) {
                $em->remove($proposeplat);
                $em->flush();
                $this->addFlash('success', 'Le plat a été supprimé avec succès');
            } else {
                $this->addFlash('danger', 'Le token CSRF est invalide. Le plat n\'a pas été supprimé');
            }
        } else {
            $this->addFlash('danger', 'Le plat n\'est pas associé à ce menu');
        }

        return $this->redirectToRoute('app_menu_index', ['id' => $idmenu]);
    }     
    /**
     * Enregistrer un Menu en base de données
     * 
     * @param   Menu     $menu
     * @param   string      $mode 
     */
    private function saveMenu(Menu $menu, string $mode, EntityManagerInterface $em): void{
        $em->persist($menu);
        $em->flush();
        
        if($mode == 'new') {
            $this->addFlash('success', 'Le menu est créé avec succès');
        } else {
            $this->addFlash('success', 'Le menu est mis à jour avec succès');
        }
    }    
}
