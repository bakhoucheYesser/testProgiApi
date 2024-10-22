<?php

namespace App\Repository;

use App\Entity\Calculations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Calculations>
 */
class CalculationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calculations::class);
    }

    /**
     * Save or update the setting in the database.
     */
    public function save(Calculations $setting): void
    {
        $this->_em->persist($setting);
        $this->_em->flush();
    }

}
