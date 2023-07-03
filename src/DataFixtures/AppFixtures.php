<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

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

        // Création d'un user
        $user = new User();
        $user->setFirstName($faker->firstName);
        $user->setLastName($faker->lastName);
        $user->setEmail("user1@productapi.com");
//        $user->setRole("ROLE_USER");
        $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
        $listUser[] = $user;
        $manager->persist($user);

        // Générer des Customers "Faker"
        for ($c=1; $c < 20; $c++) {
            $customer = new Customer();
            $customer->setName($faker->name);
            $manager->persist($customer);
            // On sauvegarde l'auteur créé dans un tableau.
            $customer->setUser($listUser[array_rand($listUser)]);
        }

        $manager->flush();
    }
}
