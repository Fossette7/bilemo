<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerFixtures extends Fixture
{
    public const CUSTOMER_REFERENCE = 'customer-';
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
      $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();

        //admin user
        $customer = new Customer();
        $customer->setUsername("Lise");
        $customer->setEmail("pomme@pommemail.com");
        $customer->setRoles(["ROLE_ADMIN"]);
        $customer->setPassword($this->userPasswordHasher->hashPassword($customer, "123456"));
        $manager->persist($customer);

        //admin user
        $customerAdmin = new Customer();
        $customerAdmin->setUsername("Vanessa");
        $customerAdmin->setEmail("admin@pommemail.com");
        $customerAdmin->setRoles(["ROLE_ADMIN"]);
        $customerAdmin->setPassword($this->userPasswordHasher->hashPassword($customerAdmin,"123456"));
        $manager->persist($customerAdmin);

        $manager->flush();
        $this->addReference($this::CUSTOMER_REFERENCE.'1', $customer);
        $this->addReference($this::CUSTOMER_REFERENCE.'2', $customerAdmin);

    }
}
