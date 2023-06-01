<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Création des Product "Faker"
        for ($p=1; $p < 20; $p++) {
            $product = new Product();
            $product->setName($faker->word);
            $product->setDescription($faker->paragraph);
            $product->setPrice($faker->randomNumber(3));
            $manager->persist($product);
        }

        // Générer des Customers "Faker"
        for ($c=1; $c < 10; $c++) {
            $customer = new Customer();
            $customer->setName($faker->name);
            $manager->persist($customer);
        }

        $manager->flush();
    }
}
