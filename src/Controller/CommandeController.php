<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ProduitRepository;
use App\Notification\ContactNotification;
use App\Services\Payement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CommandeController extends AbstractController
{


    private $session;

    private $em;

    private $repositoryCommande;

    private $tokenStorage;

    private $repository;

    private $payement;

    private $notif;


    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $em,
        CommandeRepository $repositoryCommande,
        TokenStorageInterface $tokenStorage,
        ProduitRepository $repository,
        Payement $payement,
        ContactNotification $notif
    ) {
        $this->session = $session;
        $this->em = $em;
        $this->repositoryCommande = $repositoryCommande;
        $this->tokenStorage = $tokenStorage;
        $this->repository = $repository;
        $this->payement = $payement;
        $this->notif = $notif;
    }


    /**
     * je cree un gros tableux 
     * j affecte les données dans le tableux commande
     * ensuite j'enregistre 
     */
    private function facture()
    {
        $panier = $this->session->get('panier');
        $commande = [];
        $totalHT = 0;
        $produits = $this->repository->findArray(array_keys($this->session->get('panier')));

        foreach ($produits as $produit) {
            $prixHT = ($produit->getPrice() * $panier[$produit->getId()]);
            $totalHT += $prixHT;

            $commande['produit'][$produit->getId()] = [
                'reference' => $produit->getTitre(),
                'quantite' => $panier[$produit->getId()],
                'prixHT' => round($produit->getPrice(), 2),
            ];
        }

        $commande['prixHT'] = round($totalHT, 2);
        return $commande;
    }


    /**
     * On stocke dans la base de donnée les produits 
     * on valide pas a cette étape l'achat
     * 
     * pour ne pas perdre les informations de l'utilisateur
     * 
     */
    public function prepareCommandeAction()
    {
        $this->session->remove('commande');
        if (!$this->session->has('commande')) {
            $commande = new Commande();
        } else {
            $commande = $this->repositoryCommande->find($this->session->get('commande'));
        }

        $commande->setCreatedAt(new \DateTime());
        $commande->setUser($this->getUser());
        $commande->setValider(0);
        $commande->setReference(0);
        $commande->setCommande($this->facture());


        if (!$this->session->has('commande')) {
            $this->em->persist($commande);
            $this->session->set('commande', $commande);
        }
        $this->em->flush();

        return new Response($commande->getId());
    }


    /**
     * Cette methode envoie les commandes faites par le client.
     * 
     * @Route("/commandes", name="commandes")
     * @return Response
     */
    public function commandesAction()
    {
        $user = $this->getUser();
        $commandes = $this->repositoryCommande->byFacture($user);

        if (!$user) {
            return $this->redirectToRoute('login');
        } else {
            return $this->render('commande/commande.html.twig', [
                'commandes' => $commandes,
                'current_menu' => 'commandes',
                'articles' => $this->session->has('panier') ? count($this->session->get('panier')) : $this->session->get('panier'),
                'user' => $user
            ]);
        }
    }


    /**
     * Cette methode remplace l'api banque.
     * 
     * @Route("/validationcommande/{id}", name="validationCommande")
     * @return Response
     */
    public function validationCommandeAction(int $id, Request $request)
    {
        $stripeToken = $request->request->get('stripeToken');
        $commande = $this->repositoryCommande->find($id);
        // on recupere user et on verifie si la personne qui veut acheter les produits
        // il corresponde bien a user enregisté dans la table de  commande
        // si non alors on le redirige
        // sinon success
        // on valider a 1
        // on incremente notre referance 
        // on enregistre
        // on efface les infos dans la session
        if (!$commande || $commande->getValider() == 1)
            throw new \Exception('La commande n\'existe pas');

        if ($this->getUser() !== $commande->getUser() || !$commande) {
            return $this->redirectToRoute('produit_panier');
        } else {
            $this->payement->process($commande, $this->getUser(), $stripeToken);
            $this->addFlash('success', 'Votre commande est validé avec succès');
            $commande->setValider(true);
            $commande->setReference($this->reference()); //Service
            $this->em->flush();
    
            $this->session->remove('panier');
            $this->session->remove('commande');
            // le mail de validation mais pas complaitement mis en place
            // une simple proposition Bien fonctionnelle
            // a voir ...... 
            $this->notif->notify($commande);
            return $this->redirectToRoute('factures');
        }

        return $this->render('panier/validation.html.twig', [
            'commande' => $commande,
            'articles' => $this->session->get('panier')
        ]);
    }

    /**
     * Je recupere la referance de produit
     */
    private function reference()
    {

        $reference = $this->repositoryCommande->findOneBy(array('valider' => 1), array('id' => 'DESC'), 1, 1);
        if (!$reference) {
            return 1;
        }
        return $reference->getReference() + 1;
    }
}
