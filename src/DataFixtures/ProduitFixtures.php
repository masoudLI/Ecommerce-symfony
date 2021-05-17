<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Produit;
use Faker\Factory;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 50; $i++) {
            $product = new Produit();
            $product    
                ->setTitre($faker->words(3, true))
                ->setDescription('produit ' . $i)
                ->setDisponible(true)
                ->setPrice($faker->numberBetween(100, 1000))
                ->setReference($faker->sentence(6, true))
                ->setEtat($faker->words(3, true))
                ->setCreatedAt(new \DateTime())
                ->setFilename('fake.jpg')
                ->setUpdatedAt(new \DateTime());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
