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
     * Send log to external monitoring system (admin-suite)
     *
     * @param string $value The value (message) of the log
     *
     * @return void
     */
    public function externalLog(string $value): void
    {
        if (!($_ENV['EXTERNAL_LOG_ENABLED'] == 'true')) {
            return;
        }

        // get external log config
        $externalLogUrl = $this->appUtil->getEnvValue('EXTERNAL_LOG_URL');
        $externalLogToken = $this->appUtil->getEnvValue('EXTERNAL_LOG_API_TOKEN');

        // make request to admin-suite log api
        $this->jsonUtil->getJson(
            target: $externalLogUrl . '?name=' . urlencode('code-paste: log') . '&message=' . urlencode('code-paste: ' . $value) . '&level=4',
            apiKey: $externalLogToken,
            method: 'POST'
        );
    }
}
