<?php

namespace App\Manager;

use App\Entity\Paste;
use App\Util\SiteUtil;
use App\Util\SecurityUtil;
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
    private SiteUtil $siteUtil;
    private LogManager $logManager;
    private SecurityUtil $securityUtil;
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(
        SiteUtil $siteUtil,
        LogManager $logManager,
        SecurityUtil $securityUtil,
        ErrorManager $errorManager,
        EntityManagerInterface $entityManager
    ) {
        $this->siteUtil = $siteUtil;
        $this->logManager = $logManager;
        $this->securityUtil = $securityUtil;
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
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
        // check max content length
        if (strlen($content) > 200000) {
            $this->errorManager->handleError(
                'paste content is too long',
                Response::HTTP_BAD_REQUEST
            );
        }

        // encrypt paste content
        if ($this->siteUtil->isEncryptionMode()) {
            $content = $this->securityUtil->encryptAes($content);
        }

        // create new paste entity
        $paste = new Paste();

        // set paste properties
        $paste->setToken($token);
        $paste->setContent($content);
        $paste->setTime(new \DateTime());

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

        // log new paste save
        $this->logManager->externalLog('new paste saved: ' . $token);
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
        if ($this->siteUtil->isEncryptionMode()) {
            $content = $this->securityUtil->decryptAes($content);
        }

        // return paste content
        return $content;
    }
}
