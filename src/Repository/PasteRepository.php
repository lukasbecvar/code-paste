<?php

namespace App\Repository;

use App\Entity\Paste;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * Class PasteRepository
 *
 * @extends ServiceEntityRepository<Paste>
 *
 * @package App\Repository
 */
class PasteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paste::class);
    }

    /**
     * Get paste object by token
     *
     * @param string $token The paste token
     *
     * @return Paste|null
     */
    public function getPasteByToken(string $token): ?Paste
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.token = :token')
            ->setParameter('token', $token)
            ->getQuery();

        /** @var Paste|null $result */
        $result = $query->getOneOrNullResult();

        return $result;
    }
}
