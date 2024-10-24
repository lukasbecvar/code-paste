<?php

namespace App\Tests\Util;

use App\Util\AppUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AppUtilTest
 *
 * This class tests the AppUtil class
 *
 * @package App\Tests\Util
 */
class AppUtilTest extends TestCase
{
    private AppUtil $appUtil;
    private KernelInterface $kernelInterface;

    protected function setUp(): void
    {
        // mock kernel interface
        $this->kernelInterface = $this->createMock(KernelInterface::class);

        // create instance of AppUtil
        $this->appUtil = new AppUtil($this->kernelInterface);
    }

    /**
     * Test get environment variable
     *
     * @return void
     */
    public function testGetEnvValue(): void
    {
        $result = $this->appUtil->getEnvValue('APP_ENV');
        $this->assertEquals($result, $_ENV['APP_ENV']);
    }

    /**
     * Test is SSL check
     *
     * @return void
     */
    public function testIsSsl(): void
    {
        $_SERVER['HTTPS'] = 'on';
        $this->assertTrue($this->appUtil->isSsl());

        $_SERVER['HTTPS'] = '1';
        $this->assertTrue($this->appUtil->isSsl());

        unset($_SERVER['HTTPS']);
        $this->assertFalse($this->appUtil->isSsl());
    }

    /**
     * Test check maintenance mode enabled
     *
     * @return void
     */
    public function testIsMaintenance(): void
    {
        $_ENV['MAINTENANCE_MODE'] = 'true';
        $this->assertTrue($this->appUtil->isMaintenance());

        $_ENV['MAINTENANCE_MODE'] = 'false';
        $this->assertFalse($this->appUtil->isMaintenance());
    }

    /**
     * Test check SSL only enabled
     *
     * @return void
     */
    public function testIsSslOnly(): void
    {
        $_ENV['SSL_ONLY'] = 'true';
        $this->assertTrue($this->appUtil->isSSLOnly());

        $_ENV['SSL_ONLY'] = 'false';
        $this->assertFalse($this->appUtil->isSSLOnly());
    }

    /**
     * Test check dev mode enabled
     *
     * @return void
     */
    public function testIsDevMode(): void
    {
        $_ENV['APP_ENV'] = 'dev';
        $this->assertTrue($this->appUtil->isDevMode());

        $_ENV['APP_ENV'] = 'test';
        $this->assertTrue($this->appUtil->isDevMode());

        $_ENV['APP_ENV'] = 'prod';
        $this->assertFalse($this->appUtil->isDevMode());
    }

    /**
     * Test check is encryption mode enabled
     *
     * @return void
     */
    public function testIsEncryptionMode(): void
    {
        $_ENV['ENCRYPTION_MODE'] = 'true';
        $this->assertTrue($this->appUtil->isEncryptionMode());

        $_ENV['ENCRYPTION_MODE'] = 'false';
        $this->assertFalse($this->appUtil->isEncryptionMode());
    }
}
