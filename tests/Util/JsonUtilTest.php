<?php

namespace Tests\Unit\Util;

use App\Util\JsonUtil;
use Psr\Log\LoggerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class JsonUtilTest
 *
 * Test cases for json util
 *
 * @package Tests\Unit\Util
 */
class JsonUtilTest extends TestCase
{
    private JsonUtil $jsonUtil;
    private LoggerInterface & MockObject $logger;

    protected function setUp(): void
    {
        // mock dependencies
        $this->logger = $this->createMock(LoggerInterface::class);

        // create json util instance
        $this->jsonUtil = new JsonUtil($this->logger);
    }

    /**
     * Test get json data form file
     *
     * @return void
     */
    public function testGetJsonFromFile(): void
    {
        // test with existing JSON file
        $expectedData = ['key' => 'value'];
        $filePath = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($filePath, json_encode($expectedData));

        // get JSON data from file
        $jsonData = $this->jsonUtil->getJson($filePath);

        // assert the data
        $this->assertEquals($expectedData, $jsonData);

        // clean up the test file
        unlink($filePath);
    }

    /**
     * Test get json with invalid data returned from the target
     *
     * @return void
     */
    public function testGetJsonWithInvalidDataReturned(): void
    {
        // test with invalid JSON data
        $invalidJson = '{"key": "value"';
        $filePath = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($filePath, $invalidJson);

        // get JSON data from file
        $jsonData = $this->jsonUtil->getJson($filePath);
        $this->assertEmpty($jsonData);

        // clean up the test file
        unlink($filePath);
    }
}
