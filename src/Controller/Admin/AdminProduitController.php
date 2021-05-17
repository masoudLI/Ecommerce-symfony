<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Knp\Component\Pager\PaginatorInterface;

class AdminProduitController extends AbstractController
{
    /**
     * @Route("/admin/produit", name="produit_index_admin", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, ProduitRepository $produitRepository)
    {

        $produits = $paginator->paginate(
            $produitRepository->findAllVisibleProduit(),
            $request->query->getInt('page', 1),
            10 
        );

        return $this->render('admin/produit/index.html.twig', [
            'produits' => $produits,
            'current_menu' => 'produits',
        ]);
    }

    /**
     * @Route("/admin/produit/new", name="produit_new_admin", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();

            return $this->redirectToRoute('produit_index');
        }

        return $this->render('admin/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/produit/{id}", name="produit_show_admin", methods={"GET"})
     */
    public function show(Produit $produit)
    {
        return $this->render('admin/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/admin/produit/{id}/edit", name="produit_edit_admin", methods={"GET","POST"})
     */
    public function edit(Request $request, Produit $produit): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('produit_index_admin');
        }

        return $this->render('admin/produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/produit/{id}", name="produit_delete_admin", methods={"DELETE"})
     */
    public function delete(Request $request, Produit $produit): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index_admin');
    }
}
