<?php

namespace App\Util;

/**
 * Class VisitorInfoUtil
 *
 * VisitorInfoUtil provides methods to get information about visitors
 *
 * @package App\Util
 */
class VisitorInfoUtil
{
    /**
     * Get the client's IP address
     *
     * @return string|null The client's IP address
     */
    public function getIP(): ?string
    {
        // check client IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // check forwarded IP (get IP from cloudflare visitors)
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        // default addr get
        return $_SERVER['REMOTE_ADDR'];
    }
}
