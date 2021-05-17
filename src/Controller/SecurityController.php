<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;

class SecurityController extends AbstractController {

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var CommandeRepository
     */
    private $repositoryCommande;

    /**
     * __construct
     *
     * @param  mixed $objectManager
     *
     * @return void
     */
    public function __construct(EntityManagerInterface $objectManager, SessionInterface $session, CommandeRepository $repositoryCommande)
    {
        $this->objectManager = $objectManager;
        $this->session = $session;
        $this->repositoryCommande = $repositoryCommande;
    }

    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function login (AuthenticationUtils $authenticationUtils) {

        if(!$this->session->has('panier')) {
            $this->session->set('panier', []);
        }
        $panier = $this->session->get('panier');
        
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'current_menu' => 'login',
            'panier' => $panier,
            'articles' => count($panier)
        ]);  
              
        $this->addFlash('success', 'Vous etes bien connecté');
        return $this->redirectToRoute('produit_panier'); 
    }

    /**
     * @Route("/logout", name="logout")
     * @return Response
     */
    public function logout () {
        throw new \Exception("Error Processing Request", 1);
    }


}