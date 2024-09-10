<?php

namespace App\Manager;

use App\Util\JsonUtil;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LogManager
 *
 * LogManager provides functions for sending log to external log
 *
 * @package App\Manager
 */
class LogManager
{
    private JsonUtil $jsonUtil;
    private ErrorManager $errorManager;

    public function __construct(JsonUtil $jsonUtil, ErrorManager $errorManager)
    {
        $this->jsonUtil = $jsonUtil;
        $this->errorManager = $errorManager;
    }

    /**
     * Send log to external log
     *
     * @param string $value The value of the log
     *
     * @throws \App\Exception\AppErrorException Error to send log to external log
     *
     * @return void
     */
    public function externalLog(string $value): void
    {
        if (!($_ENV['EXTERNAL_LOG_ENABLED'] == 'true')) {
            return;
        }

        // get external log config
        $externalLogUrl = $_ENV['EXTERNAL_LOG_URL'];
        $externalLogToken = $_ENV['EXTERNAL_LOG_TOKEN'];

        try {
            $this->jsonUtil->getJson(
                target: $externalLogUrl . '?token=' . $externalLogToken . '&name=' . urlencode('code-paste: log') . '&message=' . urlencode('code-paste: ' . $value) . '&level=4',
                method: 'POST'
            );
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'external-log-error: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
