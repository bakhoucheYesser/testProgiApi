<?php

namespace App\Service\FeeStrateg;

use App\Entity\VehiculeType;

class BasicFeeStrategy implements FeeStrategyInterface
{
    public function calculate(float $basePrice , VehiculeType $vehicleType):float
    {
        $basicFeeRate = $vehicleType->getBasicFeeRate();
        $basicFee = $basePrice * $basicFeeRate;

        return min(max($basicFee , $vehicleType->getBasicFeeMin()), $vehicleType->getBasicFeeMax());
    }
}