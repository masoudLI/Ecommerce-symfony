<?php

namespace App\Notification;

use App\Entity\Commande;
use Twig\Environment;

class ContactNotification
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    public function notify(Commande $commande)
    {

        $message = (new \Swift_Message())
            ->setSubject('Validation de votre commande')
            ->setFrom(['agence@gmail.com' => 'Chef.....'])
            ->setTo($commande->getUser()->getMail())
            ->setBody($this->renderer->render('emails/contact.html.twig', [
                'commande' => $commande,
                'username' => $commande->getUser()->getUsername()
            ]), 'text/html');
        $this->mailer->send($message);
    }
}
