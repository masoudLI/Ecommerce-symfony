<?php

namespace App\Controller;

use App\Entity\AdresseUser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

class PanierController extends AbstractController
{


    /**
     * @var ProduitRepository
     */
    private $produitRepository;


    /**
     * @var SessionInterface
     */
    private $session;


    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var CommandeRepository
     */
    private $repositoryCommande;

    public function __construct(ProduitRepository $produitRepository, SessionInterface $session, EntityManagerInterface $em, CommandeRepository $repositoryCommande)
    {

        $this->produitRepository = $produitRepository;
        $this->session = $session;
        $this->em = $em;
        $this->repositoryCommande = $repositoryCommande;
    }


    /**
     * Supprime le produit dans la session
     * @Route("/produit/supprimer/{id}", name="produit_supprimer")
     * @return Response
     */
    public function supprimeProduitAction(int $id)
    {

        $panier = $this->session->get('panier');
        if (array_key_exists($id, $panier)) {
            unset($panier[$id]);
            $this->session->set('panier', $panier);
            $this->addFlash('danger', 'Votre article a bien été supprimé');
        }
        return $this->redirectToRoute('produit_panier');
    }

    /**
     * Rajoute des produits dans la session
     * @Route("/produit/ajouter/{id}", name="produit_ajouter")
     * @return Response
     */
    public function ajouteProduitAction(int $id, Request $request)
    {

        // on verifie si ca existe la cle(variable) panier dans la session
        // si non on defini cette cle
        // on recupere la cle et on fait des verifications indiqués en dessous
        // ensuite on verifie si le produit existe deja dans la session en verifiant son id 
        // si oui alors on modifie juste sa quantité | on affecte a ce produit la quantité
        // sinon non on rajoute la quantité

        if (!$this->session->has('panier')) {
            $this->session->set('panier', []);
        };

        $panier = $this->session->get('panier');

        if (array_key_exists($id, $panier)) {
            // on passe directement par listing des produits
            if ($request->get('qte') !== null) {
                // on affecte | on modifie la quantité
                $panier[$id] = $request->get('qte');
                $this->addFlash('success', 'Quantité modifié avec succès');
            }
        } else {
            // sinon on ajoute la quantité
            if ($request->get('qte') !== null) {
                $panier[$id] = $request->get('qte');
            } else {
                $panier[$id] = 1;
                $this->addFlash('success', 'Votre article a bien été ajouté');
            }
        }

        
        $this->session->set('panier', $panier);
        //dd($panier);
        return $this->redirectToRoute('produit_panier');
    }

    /**
     * Affiche la page des produits 
     * @Route("/panier", name="produit_panier")
     * @return Response
     */
    public function panierAction(Request $request)
    {

        if (!$this->session->has('panier')) {
            $this->session->set('panier', []);
        }

        $panier = $this->session->get('panier');

        // on recupere les cles des produits en question dans la panier
        // on va chercher dans la table des produits les id correspondant a ces produits
        $produits = $this->produitRepository->findArray(array_keys($panier));
        return $this->render('panier/panier.html.twig', [
            'produits' => $produits,
            'articles' => count($panier),
            'panier' => $panier,
            'current_menu' => 'panier'
        ]);

        $this->render('panier/panier.html.twig');
    }

    /**
     * on appelle a notre objet commandeController 
     * ici on envoie l'entite commande a la vue
     * et on recupere les infos depuis le tableux de commande
     * 
     * @Route("/validation", name="validation")
     * @return Response
     */
    public function validationAction(CommandeController $prepareCommande, Request $request)
    {
        if (!$this->session->has('adresse')) {
            $this->session->set('adresse', []);
        }
        $adresse = $this->session->get('adresse');

        if (!$request->get('livraison') !== null && null !== $request->get('facturation')) {
            $adresse['livraison']  = $request->get('livraison');
            $adresse['facturation']  = $request->get('facturation');
        }

        $this->session->set('adresse', $adresse);
        
        $prepareCommandeAction = $prepareCommande->prepareCommandeAction();
        $commande = $this->repositoryCommande->find($prepareCommandeAction->getContent());
        $adresseLivraison = $this->getDoctrine()->getRepository(AdresseUser::class)->findOneBy(
            ['user' => $this->getUser()]
        );
        return $this->render('panier/validation.html.twig', [
            'commande' => $commande,
            'articles' => count($this->session->get('panier')),
            'adresseLivraison' => $adresseLivraison
        ]);
    }
}
