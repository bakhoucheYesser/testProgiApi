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
     *
     * @param float $basePrice
     * @return AssociationFees|null
     */
    public function findFeeForPrice(float $basePrice): ?AssociationFees
    {
        return $this->createQueryBuilder('af')
            ->where(':basePrice >= af.min_price')
            ->andWhere(':basePrice <= af.max_price OR af.max_price IS NULL')
            ->setParameter('basePrice', $basePrice)
            ->orderBy('af.min_price', 'DESC')  // Ensures the highest min_price is returned
            ->setMaxResults(1)  // Limit to one result
            ->getQuery()
            ->getOneOrNullResult();
    }

}
