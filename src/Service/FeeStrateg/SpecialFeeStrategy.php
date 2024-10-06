<?php

namespace App\Service\FeeStrateg;

use App\Entity\VehiculeType;

class SpecialFeeStrategy implements FeeStrategyInterface
{
    public function calculate(float $basePrice, VehiculeType $vehicleType): float
    {
        return $basePrice * $vehicleType->getSpecialFeeRate();
    }

}