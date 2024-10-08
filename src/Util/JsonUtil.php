<?php

namespace App\Util;

use App\Manager\ErrorManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonUtil
 *
 * JsonUtil provides functions for retrieving JSON data from a file or URL
 *
 * @package App\Util
 */
class JsonUtil
{
    private ErrorManager $errorManager;

    public function __construct(ErrorManager $errorManager)
    {
        $this->errorManager = $errorManager;
    }

    /**
     * Get JSON data from a file or URL
     *
     * @param string $target The file path or URL
     * @param string $method The HTTP method to use
     *
     * @throws \App\Exception\AppErrorException Error get json content
     *
     * @return array<mixed>|null The decoded JSON data as an associative array or null on failure
     */
    public function getJson(string $target, string $method = 'GET'): ?array
    {
        // request context
        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => [
                    'User-Agent: code-paste'
                ],
                'timeout' => 5
            ]
        ]);

        try {
            // get data
            $data = file_get_contents($target, false, $context);

            // return null if data retrieval fails
            if ($data == null) {
                return null;
            }

            // decode & return array
            return (array) json_decode($data, true);
        } catch (\Exception $e) {
            $this->errorManager->handleError(
                'error to get json: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            // return null on any exception
            return null;
        }
    }
}
