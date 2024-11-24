<?php

namespace App\Middleware;

use Exception;
use App\Manager\ErrorManager;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DatabaseOnlineMiddleware
 *
 * Middleware for check the database availability
 *
 * @package App\Middleware
 */
class DatabaseOnlineMiddleware
{
    private ErrorManager $errorManager;
    private Connection $doctrineConnection;

    public function __construct(ErrorManager $errorManager, Connection $doctrineConnection)
    {
        $this->errorManager = $errorManager;
        $this->doctrineConnection = $doctrineConnection;
    }

    /**
     * Check if database is online
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException if the database is offline
     *
     * @return void
     */
    public function onKernelRequest(): void
    {
        try {
            // select for try database connection
            $this->doctrineConnection->executeQuery('SELECT 1');
        } catch (Exception $e) {
            $this->errorManager->handleError(
                'database connection error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
