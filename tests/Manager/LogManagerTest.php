<?php

namespace App\Tests\Manager;

use App\Util\JsonUtil;
use App\Manager\LogManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class LogManagerTest
 *
 * Test class for log manager
 *
 * @package App\Tests\Manager
 */
class LogManagerTest extends TestCase
{
    private LogManager $logManager;
    private JsonUtil & MockObject $jsonUtilMock;

    protected function setUp(): void
    {
        // create mocks for dependencies
        $this->jsonUtilMock = $this->createMock(JsonUtil::class);

        // initialize LogManager with mocked dependencies
        $this->logManager = new LogManager($this->jsonUtilMock);

        // set environment variables
        $_ENV['EXTERNAL_LOG_ENABLED'] = 'true';
        $_ENV['EXTERNAL_LOG_URL'] = 'https://external-log-service.com/log';
        $_ENV['EXTERNAL_LOG_TOKEN'] = 'test-token';
    }

    /**
     * Test successful external log
     *
     * @return void
     */
    public function testExternalLogSuccess(): void
    {
        $message = 'Test log message';
        $expectedUrl = 'https://external-log-service.com/log?token=test-token&name='
            . urlencode('code-paste: log') . '&message='
            . urlencode('code-paste: ' . $message) . '&level=4';

        // expect getJson to be called with the correct URL and method
        $this->jsonUtilMock->expects($this->once())->method('getJson')->with($expectedUrl, 'POST');

        // call the method under test
        $this->logManager->externalLog($message);
    }

    /**
     * Test external log disabled
     *
     * @return void
     */
    public function testExternalLogDisabled(): void
    {
        // disable external logging
        $_ENV['EXTERNAL_LOG_ENABLED'] = 'false';

        // ensure getJson is never called when logging is disabled
        $this->jsonUtilMock->expects($this->never())->method('getJson');

        // call the method under test
        $this->logManager->externalLog('Log should not be sent');
    }
}
