<?php

namespace App\Tests\Util;

use App\Util\SiteUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class SiteUtilTest
 *
 * This class tests the SiteUtil class
 *
 * @package App\Tests\Util
 */
class SiteUtilTest extends TestCase
{
    private SiteUtil $siteUtil;
    private KernelInterface $kernelInterface;

    protected function setUp(): void
    {
        // mock kernel interface
        $this->kernelInterface = $this->createMock(KernelInterface::class);

        // create instance of SiteUtil
        $this->siteUtil = new SiteUtil($this->kernelInterface);
    }

    /**
     * Test is SSL
     *
     * @return void
     */
    public function testIsSsl(): void
    {
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue($this->siteUtil->isSsl());

        $_SERVER['HTTPS'] = '1';
        $this->assertTrue($this->siteUtil->isSsl());

        unset($_SERVER['HTTPS']);
        $this->assertFalse($this->siteUtil->isSsl());
    }

    /**
     * Test is maintenance
     *
     * @return void
     */
    public function testIsMaintenance(): void
    {
        $_ENV['MAINTENANCE_MODE'] = 'true';
        $this->assertTrue($this->siteUtil->isMaintenance());

        $_ENV['MAINTENANCE_MODE'] = 'false';
        $this->assertFalse($this->siteUtil->isMaintenance());
    }

    /**
     * Test is SSL only
     *
     * @return void
     */
    public function testIsSslOnly(): void
    {
        $_ENV['SSL_ONLY'] = 'true';
        $this->assertTrue($this->siteUtil->isSSLOnly());

        $_ENV['SSL_ONLY'] = 'false';
        $this->assertFalse($this->siteUtil->isSSLOnly());
    }

    /**
     * Test is dev mode
     *
     * @return void
     */
    public function testIsDevMode(): void
    {
        $_ENV['APP_ENV'] = 'dev';
        $this->assertTrue($this->siteUtil->isDevMode());

        $_ENV['APP_ENV'] = 'test';
        $this->assertTrue($this->siteUtil->isDevMode());

        $_ENV['APP_ENV'] = 'prod';
        $this->assertFalse($this->siteUtil->isDevMode());
    }

    /**
     * Test is encryption mode
     *
     * @return void
     */
    public function testIsEncryptionMode(): void
    {
        $_ENV['ENCRYPTION_MODE'] = 'true';
        $this->assertTrue($this->siteUtil->isEncryptionMode());

        $_ENV['ENCRYPTION_MODE'] = 'false';
        $this->assertFalse($this->siteUtil->isEncryptionMode());
    }
}
