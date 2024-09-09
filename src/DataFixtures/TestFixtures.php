<?php

namespace App\DataFixtures;

use App\Entity\Test;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TestFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $test = new Test();
        $test->setName('Test');

        $manager->persist($test);
        $manager->flush();
    }
}
