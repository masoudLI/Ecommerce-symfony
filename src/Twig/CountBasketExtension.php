<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CountBasketExtension extends AbstractExtension
{

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('countPanier', [$this, 'countPanier']),
        ];
    }

    public function countPanier()
    {
        return $this->session->has('panier') ? count($this->session->get('panier')) : '';
    }
}
