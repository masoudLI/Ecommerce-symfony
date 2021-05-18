<?php

namespace App\Controller;

use App\Entity\AdresseUser;
use App\Form\AdressFromType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LivraisonController extends AbstractController
{

    /**
     * @Route("/livraison", name="livraison")
     *
     * @return Response
     */
    public function livraison (Request $request): Response
    {
        $adresse = new AdresseUser();
        $adresses = $this->getDoctrine()->getRepository(AdresseUser::class)->findBy(['user' => $this->getUser()]);
        $form = $this->createForm(AdressFromType::class, $adresse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $adresse->setUser($this->getUser());
            $em->persist($adresse);
            $em->flush();
            $this->addFlash('success', 'Adresse a été créé avec succès');
            return $this->redirectToRoute('livraison');
        }

        return $this->render('panier/livraison.html.twig',[
            'form' => $form->createView(),
            'adresses' => $adresses
        ]);
    }
}
