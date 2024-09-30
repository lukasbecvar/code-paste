<?php

namespace App\Tests\Util;

use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class VisitorInfoUtilTest
 *
 * This class tests the VisitorInfoUtil class
 *
 * @package App\Tests\Util
 */
class VisitorInfoUtilTest extends TestCase
{
    private VisitorInfoUtil $visitorInfoUtil;

    protected function setUp(): void
    {
        // create instance of VisitorInfoUtil
        $this->visitorInfoUtil = new VisitorInfoUtil();
    }

    /**
     * Test get visitor IP address
     *
     * @return void
     */
    public function testGetIp(): void
    {
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.1.1';
        $this->assertEquals('192.168.1.1', $this->visitorInfoUtil->getIP());

        unset($_SERVER['HTTP_CLIENT_IP']);
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '192.168.1.2';
        $this->assertEquals('192.168.1.2', $this->visitorInfoUtil->getIP());

        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['REMOTE_ADDR'] = '192.168.1.3';
        $this->assertEquals('192.168.1.3', $this->visitorInfoUtil->getIP());
    }
}
