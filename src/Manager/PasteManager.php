<?php

namespace App\Manager;

use App\Entity\Paste;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PasteManager
{
    private ErrorManager $errorManager;
    private EntityManagerInterface $entityManager;

    public function __construct(ErrorManager $errorManager, EntityManagerInterface $entityManager)
    {
        $this->errorManager = $errorManager;
        $this->entityManager = $entityManager;
    }

    public function savePaste(string $token, string $content): void
    {
        $paste = new Paste();

        $paste->setToken($token);
        $paste->setContent($content);
        $paste->setTime(new \DateTime());

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
    }

    public function getPaste(string $token): ?string
    {
        $paste = $this->entityManager->getRepository(Paste::class)->findOneBy(['token' => $token]);

        if (!$paste) {
            $this->errorManager->handleError(
                'paste not found',
                Response::HTTP_NOT_FOUND
            );
        }

        return $paste->getContent();
    }
}
