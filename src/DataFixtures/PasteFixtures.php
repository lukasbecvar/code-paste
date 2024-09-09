<?php

namespace App\DataFixtures;

use App\Entity\Paste;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\ByteString;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PasteFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $test = new Paste();

        $test->setToken(ByteString::fromRandom(32));
        $test->setContent('Test');
        $test->setTime(new \DateTime());

        $manager->persist($test);
        $manager->flush();
    }
}
