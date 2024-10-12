<?php

namespace App\Util;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AppUtil
 *
 * AppUtil provides basic site-related methods
 *
 * @package App\Util
 */
class AppUtil
{
    private KernelInterface $kernelInterface;

    public function __construct(KernelInterface $kernelInterface)
    {
        $this->kernelInterface = $kernelInterface;
    }

    /**
     * Get the HTTP host
     *
     * @return string|null The HTTP host
     */
    public function getHttpHost(): ?string
    {
        return $_SERVER['HTTP_HOST'];
    }

    /** Get the application root directory
     *
     * @return string The application root directory
     */
    public function getAppRootDir(): string
    {
        return $this->kernelInterface->getProjectDir();
    }

    /**
     * Check if the connection is secure (SSL)
     *
     * @return bool Whether the connection is secure
     */
    public function isSsl(): bool
    {
        // check if HTTPS header is set and its value is either 1 or 'on'
        return isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) === 'on');
    }

    /**
     * Check if the application is in maintenance mode
     *
     * @return bool Whether the application is in maintenance mode
     */
    public function isMaintenance(): bool
    {
        return $_ENV['MAINTENANCE_MODE'] === 'true';
    }

    /**
     * Check if the ssl only mode
     *
     * @return bool Whether the application is under ssl only mode
     */
    public function isSSLOnly(): bool
    {
        return $_ENV['SSL_ONLY'] === 'true';
    }

    /**
     * Check if the application is in development mode
     *
     * @return bool Whether the application is in development mode
     */
    public function isDevMode(): bool
    {
        if ($_ENV['APP_ENV'] == 'dev' || $_ENV['APP_ENV'] == 'test') {
            return true;
        }

        return false;
    }

    /**
     * Check if is the encryption mode enabled
     *
     * @return bool Encryption mode status
     */
    public function isEncryptionMode(): bool
    {
        return $_ENV['ENCRYPTION_MODE'] === 'true';
    }

    /**
     * Get config from yaml file
     *
     * @param string $configFile The config file name
     *
     * @return mixed The config data
     */
    public function getYamlConfig(string $configFile): mixed
    {
        return Yaml::parseFile($this->getAppRootDir() . '/config/' . $configFile);
    }
}
