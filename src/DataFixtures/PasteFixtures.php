<?php

namespace App\DataFixtures;

use App\Entity\Paste;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PasteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $test = new Paste();

        $test->setToken('zSc0Uh8L1gsA7a6u');
        $test->setContent('this is a test paste');
        $test->setTime(new \DateTime());

        // persist & flush paste to database
        $manager->persist($test);
        $manager->flush();
    }
}
