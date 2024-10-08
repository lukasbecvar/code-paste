<?php

namespace App\Event\Subscriber;

use Psr\Log\LoggerInterface;
use App\Exception\AppErrorException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExceptionEventSubscriber
 *
 * The subscriber for the exception event
 *
 * @package App\Event\Subscriber
 */
class ExceptionEventSubscriber implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
    ) {
        $this->logger = $logger;
    }

    /**
     * Get the subscribed events
     *
     * @return array<string> The subscribed events
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }

    /**
     * Handle the exception event
     *
     * @param ExceptionEvent $event The exception event
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        // get the exception object
        $exception = $event->getThrowable();

        // get the error caller
        $errorCaller = $exception->getTrace()[0]['function'];

        // check if the error caller is the logger
        if ($errorCaller != 'handleError') {
            return;
        }

        // get the error message
        $message = $exception->getMessage();

        // define default exception code
        $statusCode = 500;

        // check if the object is valid exception
        if ($exception instanceof HttpException) {
            // get exception status code
            $statusCode = $exception->getStatusCode();
        }

        // check if exception code is in the excluded codes
        $excludedCodes = [400, 404, 426, 429, 501, 503];
        if (in_array($statusCode, $excludedCodes)) {
            return;
        }

        // log the error message with monolog (file storage)
        $this->logger->error($message);
    }
}
