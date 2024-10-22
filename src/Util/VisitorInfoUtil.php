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
     * Get the client IP address
     *
     * @return string|null The client IP address
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

        // default remote addr get
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Get the browser user agent
     *
     * @return string|null The user agent
     */
    public function getUserAgent(): ?string
    {
        // get user agent
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        /** @var string $browserAgent return user agent */
        $browserAgent = $userAgent !== null ? $userAgent : 'Unknown';

        // escape user agent
        $browserAgent = htmlspecialchars($browserAgent, ENT_QUOTES | ENT_HTML5);

        return $browserAgent;
    }

    /**
     * Get a short version of the browser name
     *
     * @param string|null $userAgent The user agent string
     *
     * @return string|null The short browser name
     */
    public function getBrowserShortify(string|null $userAgent = null): ?string
    {
        // set default user agent from current request
        if ($userAgent == null) {
            $userAgent = $this->getUserAgent() ?? 'Unknown';
        }

        // identify common browsers using switch statement
        switch (true) {
            case preg_match('/MSIE (\d+\.\d+);/', $userAgent):
            case str_contains($userAgent, 'MSIE'):
                $output = 'Internet Explore';
                break;
            case preg_match('/Chrome[\/\s](\d+\.\d+)/', $userAgent):
                $output = 'Chrome';
                break;
            case preg_match('/Edge\/\d+/', $userAgent):
                $output = 'Edge';
                break;
            case preg_match('/Firefox[\/\s](\d+\.\d+)/', $userAgent):
            case str_contains($userAgent, 'Firefox/96'):
                $output = 'Firefox';
                break;
            case preg_match('/Safari[\/\s](\d+\.\d+)/', $userAgent):
                $output = 'Safari';
                break;
            case str_contains($userAgent, 'UCWEB'):
            case str_contains($userAgent, 'UCBrowser'):
                $output = 'UC Browser';
                break;
            case str_contains($userAgent, 'Iceape'):
                $output = 'IceApe Browser';
                break;
            case str_contains($userAgent, 'maxthon'):
                $output = 'Maxthon Browser';
                break;
            case str_contains($userAgent, 'konqueror'):
                $output = 'Konqueror Browser';
                break;
            case str_contains($userAgent, 'NetFront'):
                $output = 'NetFront Browser';
                break;
            case str_contains($userAgent, 'Midori'):
                $output = 'Midori Browser';
                break;
            case preg_match('/OPR[\/\s](\d+\.\d+)/', $userAgent):
            case preg_match('/Opera[\/\s](\d+\.\d+)/', $userAgent):
                $output = 'Opera';
                break;
            default:
                // if not found, check user agent length
                if (str_contains($userAgent, ' ') || strlen($userAgent) >= 60) {
                    $output = 'Unknown';
                } else {
                    $output = $userAgent;
                }
        }

        return $output;
    }
}
