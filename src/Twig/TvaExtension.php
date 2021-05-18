<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TvaExtension extends AbstractExtension
{

    /**
     * @var string
     */
    private $currency;

    public function __construct(string $currency = '€')
    {
        $this->currency = $currency;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('vat', [$this, 'calculTva']),
            new TwigFilter('price_format', [$this, 'priceFormat'])
        ];
    }


    public function calculTva(float $price, ?float $tva): float
    {
        return round($price * $tva / 100, 2);
    }

    private function getVatOnly($price, $tva)
    {
        if ($tva === null) {
            return 0;
        }
        return $price * $tva / 100;
    }

    public function priceFormat(?float $price = null, ?string $currency = null)
    {
        return number_format($price, 2, ',', ' ') . ' ' . ($currency ?: $this->currency);
    }
}
