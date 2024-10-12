<?php

namespace App\DataFixtures;

use App\Entity\Paste;
use App\Util\AppUtil;
use App\Util\SecurityUtil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

/**
 * Class PasteFixtures
 *
 * Fixtures for paste entity
 *
 * @package App\DataFixtures
 */
class PasteFixtures extends Fixture
{
    private AppUtil $appUtil;
    private SecurityUtil $securityUtil;

    public function __construct(AppUtil $appUtil, SecurityUtil $securityUtil)
    {
        $this->appUtil = $appUtil;
        $this->securityUtil = $securityUtil;
    }

    public function load(ObjectManager $manager): void
    {
        $test = new Paste();

        // testing paste content
        $content = 'this is a test paste';

        // encrypt paste content
        if ($this->appUtil->isEncryptionMode()) {
            $content = $this->securityUtil->encryptAes($content);
        }

        // set paste properties
        $test->setToken('zSc0Uh8L1gsA7a6u');
        $test->setContent($content);
        $test->setTime(new \DateTime());
        $test->setIpAddress('127.0.0.1');

        // persist & flush paste to database
        $manager->persist($test);
        $manager->flush();
    }
}
