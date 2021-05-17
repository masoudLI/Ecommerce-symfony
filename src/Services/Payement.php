<?php

namespace App\Services;

use App\Api\StripeApi;
use App\Entity\User;
use App\Notification\ContactNotification;
use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use Exception;
use Mpociot\VatCalculator\VatCalculator;
use Stripe\Card;
use Stripe\Customer;

class Payement
{

    private $stripe;

    private $userRepository;

    public function __construct(StripeApi $stripe, UserRepository $userRepository)
    {
        $this->stripe = $stripe;
        $this->userRepository = $userRepository;
    }

    public function process($commande, User $user, string $token)
    {

        // Créer ou récupérer la card de l'utilisateur
        $card = $this->stripe->getCardFromToken($token);

        // on calcule le tva sur la carte utilisé
        $vatRate = (new VatCalculator())->getTaxRateForCountry($card->country) ?: 0;
        $price = floor($commande->getCommande()['prixHT'] * ((100 + $vatRate) / 100));

        // Créer ou récupérer le customer de l'utilisateur
        $customer = $this->findCustomerForUser($user, $token);
        $card = $this->getMatchingCard($customer, $card);
        if (null === $card) {
            $card = $this->string->createCardForCustomer($customer, $card);
        }

       $this->stripe->createCharge([
            "amount" => $price * 100,
            "currency" => "eur",
            "source" => $card->id,
            "customer" => $customer->id,
            "description" => "Achat sur monsite.com"
        ]); 

    }


    private function getMatchingCard(Customer $customer, Card $card): ?Card
    {
        foreach ($customer->sources->data as $datum) {
            if ($datum->fingerprint === $card->fingerprint) {
                return $datum;
            }
        }
        return null;
    }


    private function findCustomerForUser(User $user, $token)
    {
        $customerId = $this->userRepository->findCustomerForUser($user);
        if ($customerId) {
            return $this->stripe->getCustomer($customerId);
        } else {
            $customer = $this->stripe->createCustomer([
                'metadata' => [
                    'user_id' => (string) $user->getId(),
                ],
                'name'  => $user->getUsername(),
                'email'  => $user->getMail(),
                'source' => $token
            ]);
            $user->setStripeId($customer->id);
        }
        return $customer;
    }
}
