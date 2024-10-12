<?php

namespace App\Tests\Manager;

use App\Entity\Paste;
use App\Util\AppUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\PasteManager;
use App\Manager\ErrorManager;
use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;
use App\Repository\PasteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Class PasteManagerTest
 *
 * Test cases for PasteManager class
 *
 * @package App\Tests\Manager
 */
class PasteManagerTest extends TestCase
{
    private LogManager $logManager;
    private PasteManager $pasteManager;
    private SecurityUtil $securityUtil;
    private AppUtil & MockObject $appUtil;
    private ErrorManager & MockObject $errorManager;
    private VisitorInfoUtil & MockObject $visitorInfoUtil;
    private EntityManagerInterface & MockObject $entityManager;

    protected function setUp(): void
    {
        // mock dependencies
        $this->appUtil = $this->createMock(AppUtil::class);
        $this->logManager = $this->createMock(LogManager::class);
        $this->securityUtil = $this->createMock(SecurityUtil::class);
        $this->errorManager = $this->createMock(ErrorManager::class);
        $this->visitorInfoUtil = $this->createMock(VisitorInfoUtil::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // init paste manager
        $this->pasteManager = new PasteManager(
            $this->appUtil,
            $this->logManager,
            $this->securityUtil,
            $this->errorManager,
            $this->visitorInfoUtil,
            $this->entityManager
        );
    }

    /**
     * Test save paste
     *
     * @return void
     */
    public function testSavePaste(): void
    {
        // mock the entity manager persist and flush methods
        $this->entityManager->expects($this->once())
            ->method('persist')->with($this->isInstanceOf(Paste::class));
        $this->entityManager->expects($this->once())->method('flush');

        // mock appUtil to return false for encryption mode
        $this->appUtil->expects($this->once())->method('isEncryptionMode')->willReturn(false);

        // mock visitor info util to return IP address
        $this->visitorInfoUtil->expects($this->once())->method('getIP')->willReturn('127.0.0.1');

        // call the savePaste method
        $this->pasteManager->savePaste('token123', 'test content');
    }

    /**
     * Test save too long paste
     *
     * @return void
     */
    public function testSavePasteWithLongContent(): void
    {
        // mock error manager to expect handleError to be called
        $this->errorManager->expects($this->once())
            ->method('handleError')->with('paste content is too long', 400);

        // mock visitor info util to return IP address
        $this->visitorInfoUtil->expects($this->once())->method('getIP')->willReturn('127.0.0.1');

        // call the savePaste method with long content
        $this->pasteManager->savePaste('token123', str_repeat('a', 200001));
    }

    public function testGetPaste(): void
    {
        // mock repository behavior
        $paste = new Paste();
        $paste->setToken('token123');
        $paste->setContent('test content');

        $repo = $this->createMock(PasteRepository::class);
        $repo->expects($this->once())
            ->method('findOneBy')->with(['token' => 'token123'])->willReturn($paste);

        // mock entity manager
        $this->entityManager->expects($this->once())
            ->method('getRepository')->with(Paste::class)->willReturn($repo);

        // call the getPaste method and assert the content
        $content = $this->pasteManager->getPaste('token123');

        // assert the content
        $this->assertEquals('test content', $content);
    }

    /**
     * Test get paste not found
     *
     * @return void
     */
    public function testGetPasteNotFound(): void
    {
        // mock repository to return null
        $repo = $this->createMock(PasteRepository::class);
        $repo->expects($this->once())
            ->method('findOneBy')->with(['token' => 'token123'])->willReturn(null);
        $this->entityManager->expects($this->once())
            ->method('getRepository')->with(Paste::class)->willReturn($repo);

        // expect errorManager to handle not found error
        $this->errorManager->expects($this->once())->method('handleError')->with('paste not found', 404);

        // call the getPaste method and assert null return
        $content = $this->pasteManager->getPaste('token123');

        // assert result
        $this->assertNull($content);
    }
}
