<?php

namespace App\DataFixtures;

use App\Entity\Paste;
use App\Util\SiteUtil;
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
    private SiteUtil $siteUtil;
    private SecurityUtil $securityUtil;

    public function __construct(SiteUtil $siteUtil, SecurityUtil $securityUtil)
    {
        $this->siteUtil = $siteUtil;
        $this->securityUtil = $securityUtil;
    }

    public function load(ObjectManager $manager): void
    {
        $test = new Paste();

        // testing paste content
        $content = 'this is a test paste';

        // encrypt paste content
        if ($this->siteUtil->isEncryptionMode()) {
            $content = $this->securityUtil->encryptAes($content);
        }

        // set paste properties
        $test->setToken('zSc0Uh8L1gsA7a6u');
        $test->setContent($content);
        $test->setTime(new \DateTime());

        // persist & flush paste to database
        $manager->persist($test);
        $manager->flush();
    }
}
