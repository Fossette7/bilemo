<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE = 'normal-user-';

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();

        for($i=0; $i<10; $i++)
        {
            $user = new User();
            $user->setFirstname("userfirstname".$i);
            $user->setLastname("MarieRose".$i);
            $user->setEmail("test-".$i."@pommemail.com");
            $user->setCreatedAt(new \DateTime());
            $user->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_REFERENCE.'1'));
            $manager->persist($user);
            $this->addReference(self::USER_REFERENCE.$i, $user);
        }

      for($i=0; $i<5; $i++)
      {
        $user = new User();
        $user->setFirstname("username".$i);
        $user->setLastname("MariePrune".$i);
        $user->setEmail("test2-".$i."@pommemail.com");
        $user->setCreatedAt(new \DateTime());
        $user->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_REFERENCE.'2'));
        $manager->persist($user);
        $this->setReference(self::USER_REFERENCE.$i,$user);
      }

          $manager->flush();
    }

  public function getDependencies()
  {
    return [
      CustomerFixtures::class,
    ];
  }
}
