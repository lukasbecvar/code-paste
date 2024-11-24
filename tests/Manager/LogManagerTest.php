<?php

namespace App\Tests\Manager;

use App\Util\AppUtil;
use App\Util\JsonUtil;
use App\Manager\LogManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class LogManagerTest
 *
 * Test cases for log manager
 *
 * @package App\Tests\Manager
 */
class LogManagerTest extends TestCase
{
    private LogManager $logManager;
    private AppUtil & MockObject $appUtilMock;
    private JsonUtil & MockObject $jsonUtilMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->appUtilMock = $this->createMock(AppUtil::class);
        $this->jsonUtilMock = $this->createMock(JsonUtil::class);

        // create log manager instance
        $this->logManager = new LogManager($this->appUtilMock, $this->jsonUtilMock);

        // set environment variables
        $_ENV['EXTERNAL_LOG_ENABLED'] = 'true';
        $_ENV['EXTERNAL_LOG_TOKEN'] = 'test-token';
        $_ENV['EXTERNAL_LOG_URL'] = 'https://external-log-service.com/log';
    }

    /**
     * Test successful external log with success response
     *
     * @return void
     */
    public function testExternalLogWithSuccessResponse(): void
    {
        // simulate external logging is enabled
        $this->appUtilMock->method('getEnvValue')->willReturn('true');

        $message = 'Test log message';
        $expectedUrl = 'https://external-log-service.com/log?token=test-token&name='
            . urlencode('code-paste: log') . '&message='
            . urlencode('code-paste: ' . $message) . '&level=4';

        // expect get json call
        $this->jsonUtilMock->expects($this->once())->method('getJson')->with($expectedUrl, 'POST');

        // call tested method
        $this->logManager->externalLog($message);
    }

    /**
     * Test external log with logging is disabled
     *
     * @return void
     */
    public function testExternalLogWithLoggingIsDisabled(): void
    {
        // simulate external logging is disabled
        $this->appUtilMock->method('getEnvValue')->willReturn('false');

        // expect get json not to be called
        $this->jsonUtilMock->expects($this->never())->method('getJson');

        // call tested method
        $this->logManager->externalLog('Log should not be sent');
    }
}
