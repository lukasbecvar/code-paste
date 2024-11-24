<?php

namespace App\Tests\Manager;

use App\Entity\Paste;
use App\Util\AppUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\ErrorManager;
use App\Util\VisitorInfoUtil;
use App\Manager\PasteManager;
use PHPUnit\Framework\TestCase;
use App\Repository\PasteRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PasteManagerTest
 *
 * Test cases for paste manager
 *
 * @package App\Tests\Manager
 */
class PasteManagerTest extends TestCase
{
    private PasteManager $pasteManager;
    private AppUtil & MockObject $appUtilMock;
    private LogManager & MockObject $logManagerMock;
    private SecurityUtil & MockObject $securityUtilMock;
    private ErrorManager & MockObject $errorManagerMock;
    private VisitorInfoUtil & MockObject $visitorInfoUtilMock;
    private PasteRepository & MockObject $pasteRepositoryMock;
    private EntityManagerInterface & MockObject $entityManagerMock;

    protected function setUp(): void
    {
        // mock dependencies
        $this->appUtilMock = $this->createMock(AppUtil::class);
        $this->logManagerMock = $this->createMock(LogManager::class);
        $this->securityUtilMock = $this->createMock(SecurityUtil::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);
        $this->visitorInfoUtilMock = $this->createMock(VisitorInfoUtil::class);
        $this->pasteRepositoryMock = $this->createMock(PasteRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // create paste manager instance
        $this->pasteManager = new PasteManager(
            $this->appUtilMock,
            $this->logManagerMock,
            $this->securityUtilMock,
            $this->errorManagerMock,
            $this->visitorInfoUtilMock,
            $this->pasteRepositoryMock,
            $this->entityManagerMock
        );
    }

    /**
     * test save paste with success response
     *
     * @return void
     */
    public function testSavePasteWithSuccessResponse(): void
    {
        // mock expected behavior of dependencies
        $this->visitorInfoUtilMock->method('getIP')->willReturn('192.168.1.1');
        $this->visitorInfoUtilMock->method('getBrowserShortify')->willReturn('Chrome');
        $this->appUtilMock->method('isEncryptionMode')->willReturn(false);
        $this->appUtilMock->method('isSsl')->willReturn(false);
        $this->appUtilMock->method('getHttpHost')->willReturn('localhost');

        // expect entity manager to call persist and flush
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Paste::class));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        // expect log manager to be called upon successful save
        $this->logManagerMock->expects($this->once())
            ->method('externalLog');

        // call tested method
        $this->pasteManager->savePaste('sample-token', 'This is a test paste.');
    }

    /**
     * Test save paste with empty content
     *
     * @return void
     */
    public function testSavePasteWithEmptyContent(): void
    {
        // mock expected behavior of dependencies
        $this->visitorInfoUtilMock->method('getIP')->willReturn('192.168.1.1');
        $this->visitorInfoUtilMock->method('getBrowserShortify')->willReturn('Chrome');
        $this->appUtilMock->method('isEncryptionMode')->willReturn(false);

        // expect error manager call
        $this->errorManagerMock->expects($this->once())
            ->method('handleError')
            ->with('paste content is empty', Response::HTTP_BAD_REQUEST);

        // call tested method
        $this->pasteManager->savePaste('sample-token', '');
    }

    /**
     * Test save paste with content length reached maximum limit
     *
     * @return void
     */
    public function testSavePasteWithLongContent(): void
    {
        // mock expected behavior of dependencies
        $this->visitorInfoUtilMock->method('getIP')->willReturn('192.168.1.1');
        $this->visitorInfoUtilMock->method('getBrowserShortify')->willReturn('Chrome');
        $this->appUtilMock->method('isEncryptionMode')->willReturn(false);

        // expect error manager call
        $this->errorManagerMock->expects($this->once())
            ->method('handleError')
            ->with('paste content is too long', Response::HTTP_BAD_REQUEST);

        // call tested method
        $this->pasteManager->savePaste('sample-token', str_repeat('A', 200001));
    }
}
