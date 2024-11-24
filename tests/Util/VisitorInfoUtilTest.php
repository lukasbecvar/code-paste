<?php

namespace App\Tests\Util;

use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class VisitorInfoUtilTest
 *
 * Test cases for visitor info util
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
     * Test get visitor ip address from HTTP_CLIENT_IP
     *
     * @return void
     */
    public function testGetVisitorIpAddressFromHttpClientIp(): void
    {
        // set server variables
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.0.1';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '';
        $_SERVER['REMOTE_ADDR'] = '192.168.0.2';

        // call tested method
        $result = $this->visitorInfoUtil->getIP();

        // assert result
        $this->assertEquals('192.168.0.1', $result);
    }

    /**
     * Test get visitor ip address from REMOTE_ADDR
     *
     * @return void
     */
    public function testGetIpFromRemoteAddr(): void
    {
        // set the server variables
        $_SERVER['HTTP_CLIENT_IP'] = '';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '';
        $_SERVER['REMOTE_ADDR'] = '192.168.0.5';

        // call tested method
        $result = $this->visitorInfoUtil->getIP();

        // assert result
        $this->assertEquals('192.168.0.5', $result);
    }

    /**
     * Test get user agent from HTTP_USER_AGENT
     *
     * @return void
     */
    public function testGetUserAgentWithUserAgent(): void
    {
        // set server variable
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';

        // call tested method
        $result = $this->visitorInfoUtil->getUserAgent();

        // assert result
        $this->assertEquals('Mozilla/5.0', $result);
    }

    /**
     * Test get short browser name
     *
     * @return void
     */
    public function testGetShortBrowserName(): void
    {
        // call tested method
        $result = $this->visitorInfoUtil->getBrowserShortify('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.9999.999 Safari/537.36');

        // assert result
        $this->assertEquals('Chrome', $result);
    }
}
