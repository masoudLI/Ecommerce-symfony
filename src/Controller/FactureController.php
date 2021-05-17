<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Component\HttpFoundation\Response;

class FactureController extends AbstractController
{

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
     * Genere la facture de l'utilisateur
     * 
     * @Route("/factures", name="factures")
     */
    public function utilisateurFactureAction(CommandeRepository $repositoryCommande)
    {

        $factures = $repositoryCommande->byFacture($this->getUser());
        return $this->render('facture/facture.html.twig', [
            'factures' => $factures,
            'articles' => $this->session->has('panier') ? count($this->session->get('panier')) :  $this->session->get('panier')
        ]);
    }

    /**
     * Genere la pdf 
     * 
     * @Route("/facturesPDF/{id}", name="facturesPDF")
     */
    public function facturesPdfAction($id)
    {
        $facture = $this->repositoryCommande->findOneBy(
            [
                'user' => $this->getUser(),
                'valider' => 1,
                'id' => $id
            ]
        );

        if (!$facture) {
            $this->addFlash('danger', 'Une erreur est survenue');
            return $this->redirect($this->generateUrl('factures'));
        }
        // appelle a notre service 
        $this->facture($facture);

        $response = new Response();
        $response->headers->set('Content-type', 'application/pdf');

        return $response;
    }

    /**
     * Appelle a api html2pdf et genere la facture
     */
    private function facture($facture)
    {
        $html = $this->render('facture/facturePDF.html.twig', ['facture' => $facture]);
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html2pdf->pdf->SetAuthor('Admin@dev.fr');
        $html2pdf->pdf->SetTitle('Facture ' . $facture->getReference());
        $html2pdf->pdf->SetSubject('Facture Admin@dev.fr');
        $html2pdf->pdf->SetKeywords('facture,Admin@dev.fr');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($html);
        $html2pdf->Output('Facture.pdf');
    }
}
