<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Customer;

class CustomerFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $customer = new Customer();
            $customer->setFirstName($faker->firstName);
            $customer->setLastName($faker->lastName);
            $customer->setPhoneNumber($faker->phoneNumber);
            $customer->setEmail($faker->email);
            $customer->setBirthDate($faker->dateTime());
            $manager->persist($customer);
        }

        $manager->flush();
    }
}
