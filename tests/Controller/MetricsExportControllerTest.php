<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class MetricsExportControllerTest
 *
 * Test cases for metrics export controller
 *
 * @package App\Tests
 */
class MetricsExportControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test get metrics
     *
     * @return void
     */
    public function testGetMetrics(): void
    {
        // set ip address for simulate allowed ip
        $_SERVER['REMOTE_ADDR'] = '172.18.0.1';

        $this->client->request('GET', '/metrics/export');

        /** @var array<mixed> $responseData */
        $responseData = json_decode(($this->client->getResponse()->getContent() ?: '{}'), true);

        // assert response
        $this->assertArrayHasKey('pastes_count', $responseData);
        $this->assertArrayHasKey('total_paste_views', $responseData);
        $this->assertIsInt($responseData['pastes_count']);
        $this->assertIsInt($responseData['total_paste_views']);
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_OK);
    }

    /**
     * Test get metrics with forbidden ip
     *
     * @return void
     */
    public function testGetMetricsWithForbiddenIP(): void
    {
        // set ip address for simulate forbidden ip
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->client->request('GET', '/metrics/export');

        // assert response
        $this->assertResponseStatusCodeSame(JsonResponse::HTTP_FORBIDDEN);
    }
}
