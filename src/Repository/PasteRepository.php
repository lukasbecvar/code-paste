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
}
