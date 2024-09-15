<?php

namespace App\Tests\Manager;

use App\Util\JsonUtil;
use App\Manager\LogManager;
use App\Manager\ErrorManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogManagerTest
 *
 * Test class for LogManager
 *
 * @package App\Tests\Manager
 */
class LogManagerTest extends TestCase
{
    private LogManager $logManager;
    private JsonUtil & MockObject $jsonUtilMock;
    private ErrorManager & MockObject $errorManagerMock;

    protected function setUp(): void
    {
        // create mocks for dependencies
        $this->jsonUtilMock = $this->createMock(JsonUtil::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);

        // initialize LogManager with mocked dependencies
        $this->logManager = new LogManager($this->jsonUtilMock, $this->errorManagerMock);

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

    /**
     * Test external log error
     *
     * @return void
     */
    public function testExternalLogError(): void
    {
        $message = 'Test log message';

        // simulate an exception being thrown
        $this->jsonUtilMock->method('getJson')->willThrowException(
            new \Exception('External service unavailable')
        );

        // expect handleError to be called when an error occurs
        $this->errorManagerMock->expects($this->once())->method('handleError')
            ->with('external-log-error: External service unavailable', Response::HTTP_INTERNAL_SERVER_ERROR);

        // call the method under test
        $this->logManager->externalLog($message);
    }
}
