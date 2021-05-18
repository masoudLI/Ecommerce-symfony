<?php

namespace App\Api;

use Stripe\Card;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\StripeClient;
use Stripe\Stripe;

class StripeApi
{
    private StripeClient $stripe;

    public function __construct(string $privateKey)
    {
        Stripe::setApiVersion('2020-08-27');
        $this->stripe = new StripeClient($privateKey);
    }

    public function createCustomer(array $data): Customer
    {
        return $this->stripe->customers->create($data);
    }

    public function getCustomer(string $customerId): Customer
    {
        return $this->stripe->customers->retrieve($customerId);
    }

    public function getCardFromToken($token): Card
    {
        return $this->stripe->tokens->retrieve($token)->card;
    }

    public function createCardForCustomer(Customer $customer, string $token): Card
    {
        return $customer->sources->create(['source' => $token]);
    }

    public function createCharge (array $data): Charge
    {
        return $this->stripe->charges->create($data);
    }
}
