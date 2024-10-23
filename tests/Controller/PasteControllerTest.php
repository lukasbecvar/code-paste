<?php

namespace App\Tests\Controller;

use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PasteControllerTest
 *
 * Test cases for PasteController routes
 *
 * @package App\Tests\Controller
 */
class PasteControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        parent::setUp();
    }

    /**
     * Test load index page
     *
     * @return void
     */
    public function testLoadIndex(): void
    {
        // make get request
        $this->client->request('GET', '/');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorExists('img[src="/assets/images/save.svg"]');
    }

    /**
     * Test save empty paste
     *
     * @return void
     */
    public function testSaveEmptyPaste(): void
    {
        // make request
        $this->client->request('POST', '/save', [
            'paste-content' => '',
            'token' => ByteString::fromRandom(16),
        ]);

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test save new paste
     *
     * @return void
     */
    public function testSaveNewPaste(): void
    {
        // make request
        $this->client->request('POST', '/save', [
            'paste-content' => 'test content',
            'token' => ByteString::fromRandom(16),
        ]);

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test view paste
     *
     * @return void
     */
    public function testViewPaste(): void
    {
        // make get request
        $this->client->request('GET', '/view?f=zSc0Uh8L1gsA7a6u');

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('code', 'this is a test paste');
    }
}
