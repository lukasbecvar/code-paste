<?php

namespace App\Manager;

use App\Util\JsonUtil;

/**
 * Class LogManager
 *
 * LogManager provides functions for sending log to external log API
 *
 * @package App\Manager
 */
class LogManager
{
    private JsonUtil $jsonUtil;

    public function __construct(JsonUtil $jsonUtil)
    {
        $this->jsonUtil = $jsonUtil;
    }

    /**
     * Send log to external log API
     *
     * @param string $message The message of the log
     *
     * @return void
     */
    public function externalLog(string $message): void
    {
        if (($_ENV['EXTERNAL_LOG_ENABLED'] != 'true')) {
            return;
        }

        // get external log config
        $externalLogUrl = $_ENV['EXTERNAL_LOG_URL'];
        $externalLogToken = $_ENV['EXTERNAL_LOG_TOKEN'];

        // make request to external log API
        $this->jsonUtil->getJson(
            target: $externalLogUrl . '?token=' . $externalLogToken . '&name=' . urlencode('code-paste: log') . '&message=' . urlencode('code-paste: ' . $message) . '&level=4',
            method: 'POST'
        );
    }
}
