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

        // Création d'un user "normal"
        $user = new User();
        $user->setEmail("user@productapi.com");
        $user->setRole(["ROLE_USER"]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $manager->persist($user);

        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("admin@bookapi.com");
        $userAdmin->setRole(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
        $manager->persist($userAdmin);

        $manager->flush();
    }
}
