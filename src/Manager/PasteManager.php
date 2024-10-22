<?php

namespace App\Manager;

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
 * PasteManager provides paste operations
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
     * Save new paste to the database
     *
     * @param string $token The paste token
     * @param string $content The paste content
     *
     * @return void
     */
    public function savePaste(string $token, string $content): void
    {
        // get visitor info
        $ipAddress = $this->visitorInfoUtil->getIP();
        $browser = $this->visitorInfoUtil->getBrowserShortify();

        // check if IP address is null
        if ($ipAddress == null) {
            $this->errorManager->handleError(
                'error getting visitor IP address',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // check if browser is null
        if ($browser == null) {
            $this->errorManager->handleError(
                'error getting visitor browser info',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // check if content is not empty
        if ($content == null && $content == '') {
            $this->errorManager->handleError(
                'paste content is empty',
                Response::HTTP_BAD_REQUEST
            );
        }

        // check max content length
        if (strlen($content) > 200000) {
            $this->errorManager->handleError(
                'paste content is too long',
                Response::HTTP_BAD_REQUEST
            );
        }

        // encrypt paste content
        if ($this->appUtil->isEncryptionMode()) {
            $content = $this->securityUtil->encryptAes($content);
        }

        // create new paste entity
        $paste = new Paste();

        // set paste properties
        $paste->setToken($token)
            ->setContent($content)
            ->setViews(0)
            ->setTime(new \DateTime())
            ->setBrowser($browser)
            ->setIpAddress($ipAddress);

        // save paste to database
        try {
            $this->entityManager->persist($paste);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            // handle error
            $this->errorManager->handleError(
                'error saving paste: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        // get connection protocol
        $protocol = $this->appUtil->isSsl() ? 'https' : 'http';

        // log new paste save
        $this->logManager->externalLog('new paste saved: ' . $protocol . '://' . $this->appUtil->getHttpHost() . '/view?f=' . $token);
    }

    /**
     * Gets a paste from database
     *
     * @param string $token The paste token
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
                'paste not found',
                Response::HTTP_NOT_FOUND
            );
        }

        // get paste content
        $pasteId = $paste->getID();
        $content = $paste->getContent();

        // check if paste content is empty
        if ($content == null || $pasteId == null) {
            $this->errorManager->handleError(
                'paste content is empty',
                Response::HTTP_INTERNAL_SERVER_ERROR
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
     * @return void
     */
    public function increastePasteViews(int $id): void
    {
        // get paste from database
        $paste = $this->entityManager->getRepository(Paste::class)->find($id);

        // check if paste exists
        if (!$paste) {
            $this->errorManager->handleError(
                'paste not found',
                Response::HTTP_NOT_FOUND
            );
        }

        // get paste views
        $views = $paste->getViews();

        // increate paste views
        $paste->setViews($views + 1);

        // save paste to database
        try {
            $this->entityManager->persist($paste);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            // handle exception
            $this->errorManager->handleError(
                'error to increase paste views: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
