<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\Produit;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TimeExtension extends AbstractExtension
{


    private $tokenStorage;


    public function __construct( TokenStorageInterface $tokenStorage )
    {
        $this->tokenStorage = $tokenStorage;
    }
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('formatAt', [$this, 'formatAt']),
        ];
    }

    public function formatAt ($format = 'Y/m/d H:i:s') {
        $date = new \DateTime();
        $date->setTimeZone(new \DateTimeZone('Europe/Paris'));
        return $date->format($format);
    }
}
