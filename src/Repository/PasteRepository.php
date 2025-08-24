<?php

namespace App\Repository;

use DateTime;
use Exception;
use DateInterval;
use App\Entity\Paste;
use InvalidArgumentException;
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
        $query = $this->createQueryBuilder('u')->where('u.token = :token')->setParameter('token', $token)->getQuery();

        /** @var Paste|null $result */
        $result = $query->getOneOrNullResult();

        return $result;
    }

    /**
     * Get pastes list by time period
     *
     * @param string $filter The filter for the time period
     *
     * @return array<mixed> An array of pastes
     *
     * @throws InvalidArgumentException If the filter is not valid
     */
    public function findByTimeFilter(string $filter): array
    {
        $now = new DateTime();
        $startDate = null;

        // calculate start date based on the filter
        switch ($filter) {
            case 'H':
                $startDate = $now->sub(new DateInterval('PT1H'));
                break;
            case 'D':
                $startDate = $now->sub(new DateInterval('P1D'));
                break;
            case 'W':
                $startDate = $now->sub(new DateInterval('P7D'));
                break;
            case 'M':
                $startDate = $now->sub(new DateInterval('P1M'));
                break;
            case 'Y':
                $startDate = $now->sub(new DateInterval('P1Y'));
                break;
            case 'ALL':
                return $this->findAll();
            default:
                throw new InvalidArgumentException("Invalid filter: $filter");
        }

        // create a query builder
        $qb = $this->createQueryBuilder('v');
        $qb->where('v.time >= :time')->setParameter('time', $startDate);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get the sum of views from all pastes
     *
     * @return int The total views count
     */
    public function getTotalViews(): int
    {
        // query builder to sum views column
        $qb = $this->createQueryBuilder('p');
        $qb->select('SUM(p.views) AS totalViews');

        try {
            // execute query and get result
            $result = $qb->getQuery()->getSingleScalarResult();
        } catch (Exception) {
            return 0;
        }

        return (int) $result;
    }
}
