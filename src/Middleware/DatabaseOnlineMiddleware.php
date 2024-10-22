<?php

namespace App\Middleware;

use App\Manager\ErrorManager;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DatabaseOnlineMiddleware
 *
 * The middleware for check the availability of the database
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
     * @throws Symfony\Component\HttpKernel\Exception\HttpException if the database is offline
     * 
     * @return void
     */
    public function onKernelRequest(): void
    {
        try {
            // select for try database connection
            $this->doctrineConnection->executeQuery('SELECT 1');
        } catch (\Exception $e) {
            // handle error if database not connected
            $this->errorManager->handleError(
                'database connection error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
