<?php

namespace App\Middleware;

use App\Util\AppUtil;
use Psr\Log\LoggerInterface;
use App\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class MaintenanceMiddleware
 *
 * Middleware for maintenance mode handling
 *
 * @package App\Middleware
 */
class MaintenanceMiddleware
{
    private AppUtil $appUtil;
    private LoggerInterface $logger;
    private ErrorManager $errorManager;

    public function __construct(AppUtil $appUtil, LoggerInterface $loggerInterface, ErrorManager $errorManager)
    {
        $this->appUtil = $appUtil;
        $this->logger = $loggerInterface;
        $this->errorManager = $errorManager;
    }

    /**
     * Check if app is maintenance mode
     *
     * @param RequestEvent $event The request event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // check if maintenance mode is enabled
        if ($this->appUtil->isMaintenance()) {
            // handle debug mode exception
            if ($this->appUtil->isDevMode()) {
                $this->errorManager->handleError(
                    msg: 'the application is under maintenance mode',
                    code: Response::HTTP_SERVICE_UNAVAILABLE
                );
            } else {
                $this->logger->error('the application is under maintenance mode');
            }

            // render maintenance page
            $content = $this->errorManager->getErrorView('maintenance');
            $response = new Response($content, Response::HTTP_SERVICE_UNAVAILABLE);
            $event->setResponse($response);
        }
    }
}
