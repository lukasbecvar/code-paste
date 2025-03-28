<?php

namespace App\Manager;

use DateTime;
use Exception;
use App\Entity\Paste;
use App\Util\AppUtil;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
use App\Repository\PasteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PasteManager
 *
 * Manager for paste save/get functionality
 *
 * @package App\Manager
 */
class PasteManager
{
    private AppUtil $appUtil;
    private LogManager $logManager;
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private VisitorInfoUtil $visitorInfoUtil;
    private PasteRepository $pasteRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AppUtil $appUtil,
        LogManager $logManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        VisitorInfoUtil $visitorInfoUtil,
        PasteRepository $pasteRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->appUtil = $appUtil;
        $this->logManager = $logManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->pasteRepository = $pasteRepository;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Save new paste to database
     *
     * @param string $token The paste token
     * @param string $content The paste content
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If the paste save failed
     *
     * @return void
     */
    public function savePaste(string $token, string $content): void
    {
        // get visitor info
        $ipAddress = $this->visitorInfoUtil->getIP();
        $browser = $this->visitorInfoUtil->getBrowserShortify();

        // check if visitor ip address is null
        if ($ipAddress == null) {
            $this->errorManager->handleError(
                msg: 'error getting visitor IP address',
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // check if visitor browser is null
        if ($browser == null) {
            $this->errorManager->handleError(
                msg: 'error getting visitor browser info',
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // check if paste content is empty
        if (empty($content)) {
            $this->errorManager->handleError(
                msg: 'paste content is empty',
                code: Response::HTTP_BAD_REQUEST
            );
        }

        // check max content length reached
        if (strlen($content) > 200000) {
            $this->errorManager->handleError(
                msg: 'paste content is too long',
                code: Response::HTTP_BAD_REQUEST
            );
        }

        // encrypt paste content
        if ($this->appUtil->isEncryptionMode()) {
            $content = $this->securityUtil->encryptAes($content);
        }

        // create new paste entity
        $paste = new Paste();
        $paste->setToken($token)
            ->setContent($content)
            ->setViews(0)
            ->setTime(new DateTime())
            ->setBrowser($browser)
            ->setIpAddress($ipAddress);

        try {
            // save paste to database
            $this->entityManager->persist($paste);
            $this->entityManager->flush();
        } catch (Exception $e) {
            $this->errorManager->handleError(
                msg: 'error saving paste: ' . $e->getMessage(),
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // get connection protocol
        $protocol = $this->appUtil->isSsl() ? 'https' : 'http';

        // log new paste save to external log
        $this->logManager->externalLog('new paste saved: ' . $protocol . '://' . $this->appUtil->getHttpHost() . '/view?f=' . $token);
    }

    /**
     * Gets paste from database
     *
     * @param string $token The paste token
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If the paste get failed
     *
     * @return string|null The paste content
     */
    public function getPaste(string $token): ?string
    {
        // get paste from database
        $paste = $this->pasteRepository->getPasteByToken($token);

        // check if paste exists
        if (!$paste) {
            $this->errorManager->handleError(
                msg: 'paste not found',
                code: Response::HTTP_NOT_FOUND
            );
        }

        // get paste content
        $pasteId = $paste->getID();
        $content = $paste->getContent();

        // check if paste content is empty
        if ($content == null || $pasteId == null) {
            $this->errorManager->handleError(
                msg: 'paste content is empty',
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // increase paste views
        $this->increastePasteViews($pasteId);

        // decrypt paste content
        if ($this->appUtil->isEncryptionMode()) {
            $content = $this->securityUtil->decryptAes($content);
        }

        // return paste content
        return $content;
    }

    /**
     * Increase paste views counter
     *
     * @param int $id The id of the paste row
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException If the counter update failed
     *
     * @return void
     */
    public function increastePasteViews(int $id): void
    {
        // get paste from database
        $paste = $this->entityManager->getRepository(Paste::class)->find($id);

        // check if paste exists
        if (!$paste) {
            $this->errorManager->handleError(
                msg: 'paste not found',
                code: Response::HTTP_NOT_FOUND
            );
        }

        // get current paste views
        $views = $paste->getViews();

        // increate paste views
        $paste->setViews($views + 1);

        try {
            // save updated paste views to database
            $this->entityManager->persist($paste);
            $this->entityManager->flush();
        } catch (Exception $e) {
            $this->errorManager->handleError(
                msg: 'error to increase paste views: ' . $e->getMessage(),
                code: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get count of pastes by time period
     *
     * @return int The number of pastes
     */
    public function getPastesCountByTimePeriod(string $filter): int
    {
        return count($this->pasteRepository->findByTimeFilter($filter));
    }

    /**
     * Get total views count
     *
     * @return int The total views count
     */
    public function getTotalViews(): int
    {
        return $this->pasteRepository->getTotalViews();
    }
}
