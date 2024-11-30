<?php

namespace App\Tests\Controller;

use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PasteControllerTest
 *
 * Test cases for paste save/view controller
 *
 * @package App\Tests\Controller
 */
class PasteControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Test load index page (show save paste view)
     *
     * @return void
     */
    public function testLoadIndexPage(): void
    {
        $this->client->request('GET', '/');

        // assert response
        $this->assertSelectorExists('img[src="/assets/images/save.svg"]');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    /**
     * Test save empty paste
     *
     * @return void
     */
    public function testSaveEmptyPaste(): void
    {
        $this->client->request('POST', '/save', [
            'paste-content' => '',
            'token' => ByteString::fromRandom(16),
        ]);

        // assert response
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * Test save new paste with success response
     *
     * @return void
     */
    public function testSaveNewPasteSuccess(): void
    {
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
        $this->client->request('GET', '/view?f=zSc0Uh8L1gsA7a6u');

        // assert response
        $this->assertSelectorTextContains('code', 'this is a test paste');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
