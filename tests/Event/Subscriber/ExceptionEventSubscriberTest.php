<?php

namespace App\Tests\Event\Subscriber;

use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use App\Event\Subscriber\ExceptionEventSubscriber;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

/**
 * Class ExceptionEventSubscriberTest
 *
 * Test the exception event subscriber
 *
 * @package App\Tests\Event\Subscriber
 */
class ExceptionEventSubscriberTest extends TestCase
{
    private LoggerInterface & MockObject $logger;
    private ExceptionEventSubscriber $subscriber;

    protected function setUp(): void
    {
        // mock logger
        $this->logger = $this->createMock(LoggerInterface::class);

        // create instance of ExceptionEventSubscriber
        $this->subscriber = new ExceptionEventSubscriber($this->logger);
    }

    /**
     * Test exception event handling
     *
     * @return void
     */
    public function testExceptionEventHandling(): void
    {
        $exception = new \Exception('Unknown database error');
        $trace = [['function' => 'handleError']];
        $reflector = new \ReflectionClass($exception);
        $property = $reflector->getProperty('trace');
        $property->setAccessible(true);
        $property->setValue($exception, $trace);

        // create a new exception event
        /** @var HttpKernelInterface $kernel */
        $kernel = $this->createMock(HttpKernelInterface::class);
        /** @var Request $request */
        $request = $this->createMock(Request::class);
        $event = new ExceptionEvent(
            $kernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        // check if the logger logs the error message
        $this->logger->expects($this->once())->method('error')->with('Unknown database error');

        // handle the exception event
        $this->subscriber->onKernelException($event);
    }
}
