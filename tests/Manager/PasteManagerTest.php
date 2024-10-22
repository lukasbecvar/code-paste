<?php

namespace App\Tests\Manager;

use App\Entity\Paste;
use App\Util\AppUtil;
use App\Util\SecurityUtil;
use App\Manager\LogManager;
use App\Manager\ErrorManager;
use App\Util\VisitorInfoUtil;
use App\Manager\PasteManager;
use App\Repository\PasteRepository;
use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PasteManagerTest
 *
 * Test for paste storage manager
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
        // create mocks for dependencies
        $this->appUtilMock = $this->createMock(AppUtil::class);
        $this->logManagerMock = $this->createMock(LogManager::class);
        $this->securityUtilMock = $this->createMock(SecurityUtil::class);
        $this->errorManagerMock = $this->createMock(ErrorManager::class);
        $this->visitorInfoUtilMock = $this->createMock(VisitorInfoUtil::class);
        $this->pasteRepositoryMock = $this->createMock(PasteRepository::class);
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);

        // instantiate the PasteManager with the mocked dependencies
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
     * test successful paste saving
     *
     * @return void
     */
    public function testSavePasteSuccess(): void
    {
        // set up expected behavior of mocks
        $this->visitorInfoUtilMock->method('getIP')->willReturn('192.168.1.1');
        $this->visitorInfoUtilMock->method('getBrowserShortify')->willReturn('Chrome');
        $this->appUtilMock->method('isEncryptionMode')->willReturn(false);
        $this->appUtilMock->method('isSsl')->willReturn(false);
        $this->appUtilMock->method('getHttpHost')->willReturn('localhost');

        // create a mock for the Paste entity
        $pasteMock = $this->createMock(Paste::class);
        $pasteMock->method('setToken')->willReturnSelf();
        $pasteMock->method('setContent')->willReturnSelf();
        $pasteMock->method('setViews')->willReturnSelf();
        $pasteMock->method('setTime')->willReturnSelf();
        $pasteMock->method('setBrowser')->willReturnSelf();
        $pasteMock->method('setIpAddress')->willReturnSelf();

        // expect the entityManager to call persist and flush
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Paste::class));
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        // Eexpect the logManager to be called upon successful save
        $this->logManagerMock->expects($this->once())
            ->method('externalLog');

        // call the method under test
        $this->pasteManager->savePaste('sample-token', 'This is a test paste.');
    }

    /**
     * Test save paste with empty content
     *
     * @return void
     */
    public function testSavePasteWithEmptyContent(): void
    {
        // set up expected behavior of mocks
        $this->visitorInfoUtilMock->method('getIP')->willReturn('192.168.1.1');
        $this->visitorInfoUtilMock->method('getBrowserShortify')->willReturn('Chrome');
        $this->appUtilMock->method('isEncryptionMode')->willReturn(false);

        // expect the ErrorManager to call handleError
        $this->errorManagerMock->expects($this->once())
            ->method('handleError')
            ->with('paste content is empty', Response::HTTP_BAD_REQUEST);

        // call the method under test
        $this->pasteManager->savePaste('sample-token', '');
    }

    /**
     * Test save paste with content length reached maximum limit
     *
     * @return void
     */
    public function testSavePasteWithLongContent(): void
    {
        // set up expected behavior of mocks
        $this->visitorInfoUtilMock->method('getIP')->willReturn('192.168.1.1');
        $this->visitorInfoUtilMock->method('getBrowserShortify')->willReturn('Chrome');
        $this->appUtilMock->method('isEncryptionMode')->willReturn(false);

        // expect the ErrorManager to call handleError
        $this->errorManagerMock->expects($this->once())
            ->method('handleError')
            ->with('paste content is too long', Response::HTTP_BAD_REQUEST);

        // call the method under test
        $this->pasteManager->savePaste('sample-token', str_repeat('A', 200001));
    }
}
