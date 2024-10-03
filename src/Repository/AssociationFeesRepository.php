<?php

namespace App\Repository;

use App\Entity\AssociationFees;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AssociationFees>
 */
class AssociationFeesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssociationFees::class);
    }

    /**
     * Find the appropriate fee for a given base price.
     *
     * @param float $basePrice The base price of the vehicle.
     * @return AssociationFees|null The matching association fee entity or null if not found.
     */
    public function findFeeForPrice(float $basePrice): ?AssociationFees
    {
        // Query to find the appropriate fee based on the base price
        return $this->createQueryBuilder('af')
            ->where(':basePrice >= af.min_price')
            ->andWhere(':basePrice <= af.max_price OR af.max_price IS NULL')
            ->setParameter('basePrice', $basePrice)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
