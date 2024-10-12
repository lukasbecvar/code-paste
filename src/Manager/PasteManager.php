<?php

namespace App\Manager;

use App\Entity\Paste;
use App\Util\AppUtil;
use App\Util\SecurityUtil;
use App\Util\VisitorInfoUtil;
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
    private EntityManagerInterface $entityManager;

    public function __construct(
        AppUtil $appUtil,
        LogManager $logManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        VisitorInfoUtil $visitorInfoUtil,
        EntityManagerInterface $entityManager
    ) {
        $this->appUtil = $appUtil;
        $this->logManager = $logManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
        $this->visitorInfoUtil = $visitorInfoUtil;
    }

    /**
     * Saves a paste to database
     *
     * @param string $token The paste token
     * @param string $content The paste content
     *
     * @return void
     */
    public function savePaste(string $token, string $content): void
    {
        // get visitor IP address
        $ipAddress = $this->visitorInfoUtil->getIP();

        // check if IP address is null
        if ($ipAddress == null) {
            $this->errorManager->handleError(
                'error getting visitor IP address',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            return;
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
        $paste->setToken($token);
        $paste->setContent($content);
        $paste->setTime(new \DateTime());
        $paste->setIpAddress($ipAddress);

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
        $paste = $this->entityManager->getRepository(Paste::class)->findOneBy(['token' => $token]);

        // check if paste exists
        if (!$paste) {
            $this->errorManager->handleError(
                'paste not found',
                Response::HTTP_NOT_FOUND
            );
            return null;
        }

        // get paste content
        $content = $paste->getContent();

        // check if paste content is empty
        if ($content == null) {
            $this->errorManager->handleError(
                'paste content is empty',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
            return null;
        }

        // decrypt paste content
        if ($this->appUtil->isEncryptionMode()) {
            $content = $this->securityUtil->decryptAes($content);
        }

        // return paste content
        return $content;
    }
}
