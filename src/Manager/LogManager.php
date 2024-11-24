<?php

namespace App\Manager;

use App\Util\AppUtil;
use App\Util\JsonUtil;

/**
 * Class LogManager
 *
 * Manager for sending log to external log API (admin-suite)
 *
 * @package App\Manager
 */
class LogManager
{
    private AppUtil $appUtil;
    private JsonUtil $jsonUtil;

    public function __construct(AppUtil $appUtil, JsonUtil $jsonUtil)
    {
        $this->appUtil = $appUtil;
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
        // check if external log is enabled
        if ($this->appUtil->getEnvValue('EXTERNAL_LOG_ENABLED') != 'true') {
            return;
        }

        // get external log config
        $externalLogUrl = $_ENV['EXTERNAL_LOG_URL'];
        $externalLogToken = $_ENV['EXTERNAL_LOG_TOKEN'];

        // request to external log API
        $this->jsonUtil->getJson(
            target: $externalLogUrl . '?token=' . $externalLogToken . '&name=' . urlencode('code-paste: log') . '&message=' . urlencode('code-paste: ' . $message) . '&level=4',
            method: 'POST'
        );
    }
}
