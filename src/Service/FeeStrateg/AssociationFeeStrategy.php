<?php

namespace App\Service\FeeStrateg;

use App\Entity\VehiculeType;
use App\Repository\AssociationFeesRepository;
use Exception;

class AssociationFeeStrategy implements FeeStrategyInterface
{
    private AssociationFeesRepository $associationFeesRepository;

    public function __construct(AssociationFeesRepository $associationFeesRepository)
    {
        $this->associationFeesRepository = $associationFeesRepository;
    }

    /**
     * @throws Exception
     */
    public function calculate(float $basePrice, VehiculeType $vehicleType): float
    {
        $fee = $this->associationFeesRepository->findFeeForPrice($basePrice);
        if ($fee === null) {
            throw new Exception('No association fee found for the given base price.');
        }
        return $fee->getAssociationFee();
    }

}