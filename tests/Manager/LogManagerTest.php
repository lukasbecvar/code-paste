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
    }

    /**
     * Test send log message to external log api when external log disabled
     *
     * @return void
     */
    public function testSendLogMessageToExternalLogApiWhenExternalLogDisabled(): void
    {
        // set external log config
        $_ENV['EXTERNAL_LOG_ENABLED'] = 'false';
        $_ENV['EXTERNAL_LOG_URL'] = 'https://external-log-service.com/log';
        $_ENV['EXTERNAL_LOG_API_TOKEN'] = 'test-token';

        // log message
        $value = 'This is a test log message';

        // expect json util get json call
        $this->jsonUtilMock->expects($this->never())->method('getJson');

        // call tested method
        $this->logManager->externalLog($value);
    }

    /**
     * Test send log message to external log api when external log enabled
     *
     * @return void
     */
    public function testSendLogMessageToExternalLogApiWhenExternalLogEnabled(): void
    {
        // set external log config
        $_ENV['EXTERNAL_LOG_ENABLED'] = 'true';
        $_ENV['EXTERNAL_LOG_URL'] = 'https://external-log-service.com/log';
        $_ENV['EXTERNAL_LOG_API_TOKEN'] = 'test-token';

        // log message
        $value = 'This is a test log message';

        // expect json util get json call
        $this->jsonUtilMock->expects($this->once())->method('getJson');

        // call tested method
        $this->logManager->externalLog($value);
    }
}
