<?php

namespace App\Tests\Event\Subscriber;

use App\Util\AppUtil;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use App\Controller\ErrorController;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Event\Subscriber\ExceptionEventSubscriber;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ExceptionEventSubscriberTest
 *
 * Test the exception event subscriber
 *
 * @package App\Tests\Event\Subscriber
 */
class ExceptionEventSubscriberTest extends TestCase
{
    private AppUtil & MockObject $appUtil;
    private LoggerInterface & MockObject $logger;
    private ExceptionEventSubscriber $subscriber;
    private ErrorController & MockObject $errorController;

    protected function setUp(): void
    {
        // mock dependencies
        $this->appUtil = $this->createMock(AppUtil::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->errorController = $this->createMock(ErrorController::class);

        // mock config
        $this->appUtil->method('getYamlConfig')->willReturn([
            'monolog' => [
                'handlers' => [
                    'filtered' => [
                        'excluded_http_codes' => [404, 405, 429, 503]
                    ]
                ]
            ]
        ]);

        // create instance of the ExceptionEventSubscriber
        $this->subscriber = new ExceptionEventSubscriber($this->appUtil, $this->logger, $this->errorController);
    }

    /**
     * Test log error for non excluded http code
     *
     * @return void
     */
    public function testOnKernelExceptionLogsErrorForNonExcludedHttpCode(): void
    {
        // expect that logger->error() will be called once
        $this->logger->expects($this->once())->method('error')->with('Test Exception Message');

        // expect error controller call
        $this->errorController->expects($this->once())->method('show');

        // create instance of HttpException
        $exception = new HttpException(Response::HTTP_INTERNAL_SERVER_ERROR, 'Test Exception Message');

        /** @var HttpKernelInterface & MockObject $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        /** @var Request & MockObject $request */
        $request = $this->createMock(Request::class);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        // call the onKernelException method
        $this->subscriber->onKernelException($event);
    }

    /**
     * Test does not log error for excluded http code
     *
     * @return void
     */
    public function testOnKernelExceptionDoesNotLogExcludedHttpCode(): void
    {
        // expect that logger->error() will NOT be called
        $this->logger->expects($this->never())->method('error');

        // expect error controller call
        $this->errorController->expects($this->once())->method('show');

        // create instance of HttpException
        $exception = new HttpException(404, 'Test Exception Message');
        /** @var HttpKernelInterface & MockObject $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        /** @var Request & MockObject $request */
        $request = $this->createMock(Request::class);
        $event = new ExceptionEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST, $exception);

        // call the onKernelException method
        $this->subscriber->onKernelException($event);
    }
}
