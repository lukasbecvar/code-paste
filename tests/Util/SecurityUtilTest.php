<?php

namespace App\Tests\Util;

use App\Util\SecurityUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class SecurityUtilTest
 *
 * Test cases for security util
 *
 * @package App\Tests\Util
 */
class SecurityUtilTest extends TestCase
{
    private SecurityUtil $securityUtil;

    protected function setUp(): void
    {
        $_ENV['APP_SECRET'] = 'test_secret';

        // create instance of SecurityUtil
        $this->securityUtil = new SecurityUtil();
    }

    /**
     * Test escape XSS attacks
     *
     * @return void
     */
    public function testEscapeString(): void
    {
        $input = '<script>alert("xss")</script>';
        $expectedOutput = '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;';

        // assert result
        $this->assertEquals($expectedOutput, $this->securityUtil->escapeString($input));
    }

    /**
     * Test encrypt AES
     *
     * @return void
     */
    public function testEncryptAes(): void
    {
        $plainText = 'my_secret_data';
        $encrypted = $this->securityUtil->encryptAes($plainText);

        // assert result
        $this->assertNotEquals($plainText, $encrypted);
    }

    /**
     * Test decrypt AES
     *
     * @return void
     */
    public function testDecryptAes(): void
    {
        $plainText = 'my_secret_data';
        $encrypted = $this->securityUtil->encryptAes($plainText);
        $decrypted = $this->securityUtil->decryptAes($encrypted);

        // assert result
        $this->assertEquals($plainText, $decrypted);
    }
}
