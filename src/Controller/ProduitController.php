<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProduitController extends AbstractController
{


    /**
     * @var ProduitRepository
     */
    private $produitRepository;

    /**
     * @var SessionInterface
     */
    private $session;


    public function __construct(ProduitRepository $produitsRepository, SessionInterface $session)
    {

        $this->produitRepository = $produitsRepository;
        $this->session = $session;
    }

    /**
     * @Route("/", name="produit_index")
     * @return Response
     */
    public function produitAction(PaginatorInterface $paginator, Request $request)
    {

        if (!$this->session->has('panier')) {
            $this->session->set('panier', []);
        }
        $panier = $this->session->get('panier');

        $produits = $paginator->paginate(
            $this->produitRepository->findAllVisibleProduit(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'current_menu' => 'produits',
            'panier' => $panier,
            'articles' => count($panier)
        ]);
    }

    /**
     * @Route("/produit/{slug}-{id}", name="produit_show", requirements={"slug": "[a-z0-9\-]*"})
     * @return Response
     */
    public function produitShowAction(int $id, string $slug)
    {

        if (!$this->session->has('panier')) {
            $this->session->set('panier', []);
        }

        $panier = $this->session->get('panier');

        $produit = $this->produitRepository->find($id);

        if ($produit->getSlug() !== $slug) {
            $this->redirectToRoute('produit_show', [
                'slug' => $produit->getSlug(),
                'id' => $produit->getId()
            ]);
        }
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'current_menu' => 'login',
            'articles' => count($panier),
            'panier' => $panier
        ]);
    }
}
