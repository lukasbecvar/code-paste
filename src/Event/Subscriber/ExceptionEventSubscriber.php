<?php

namespace App\Event\Subscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
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
        // get the error caller
        $errorCaler = $event->getThrowable()->getTrace()[0]['function'];

        // get the error message
        $message = $event->getThrowable()->getMessage();

        // check if the error caller is the logger
        if ($errorCaler != 'handleError') {
            return;
        }

        // log the error message with monolog (file storage)
        $this->logger->error($message);
    }
}
