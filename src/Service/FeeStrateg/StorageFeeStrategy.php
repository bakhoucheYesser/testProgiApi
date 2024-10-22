<?php

namespace App\Service\FeeStrateg;

use App\Entity\VehiculeType;

class StorageFeeStrategy implements FeeStrategyInterface
{

    public function calculate(float $basePrice, VehiculeType $vehicleType): float
    {
        // TODO: Implement calculate() method.
        return 100;
    }
}