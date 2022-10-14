<?php

namespace App\DataFixtures;

use App\Entity\Mobile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();

      $mobilesNamebrand = [
        0 => 'Samsouche',
        1 => 'Appelle',
        2 => 'Guignool',
        3 => 'Ericsonne',
        4 => 'Ouahouais',
        5 => 'Bluesberry'
        ];

        for ($i = 0; $i <= 5; $i++) {
          $telephone = new Mobile();
          $telephone->setBrandname($mobilesNamebrand[$i]);
          $telephone->setModel("model de type".$i);
          $telephone->setDescription("Je suis un mobile avec une description".$i);
          $telephone->setPrice(rand(300,850));
          $telephone->setCreatedAt(new \DateTime());
          $telephone->setPicture("photo du produit".$i);
          $telephone->setStock($i);

          $manager->persist($telephone);
        }
        $manager->flush();
    }
}
