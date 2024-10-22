<?php

namespace App\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ErrorControllerTest
 *
 * Test cases for handling different error pages
 *
 * @package App\Tests
 */
class ErrorControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test the default error page
     *
     * @return void
     */
    public function testErrorDefault(): void
    {
        // make get request
        $this->client->request('GET', '/error');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('.error-page-msg', 'Unknown error, please contact the service administrator');
    }

    /**
     * Test error block for maintenance mode
     *
     * @return void
     */
    public function testErrorBlockMaintenance(): void
    {
        // make get request
        $this->client->request('GET', '/error?code=maintenance');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('.error-page-msg', 'Unknown error, please contact the service administrator');
    }

    /**
     * Test error for Bad Request (400)
     *
     * @return void
     */
    public function testError400(): void
    {
        // make get request
        $this->client->request('GET', '/error?code=400');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('.error-page-msg', 'Error: Bad request');
    }

    /**
     * Test error for Page Not Found (404)
     *
     * @return void
     */
    public function testError404(): void
    {
        // make get request
        $this->client->request('GET', '/error?code=404');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('.error-page-msg', 'Error: Page not found');
    }

    /**
     * Test error for Too Many Requests (429)
     *
     * @return void
     */
    public function testError429(): void
    {
        // make get request
        $this->client->request('GET', '/error?code=429');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('.error-page-msg', 'Error: Too Many Requests');
    }

    /**
     * Test error for Internal Server Error (500)
     *
     * @return void
     */
    public function testError500(): void
    {
        // make get request
        $this->client->request('GET', '/error?code=500');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('.error-page-msg', 'Internal Server Error');
    }
}
