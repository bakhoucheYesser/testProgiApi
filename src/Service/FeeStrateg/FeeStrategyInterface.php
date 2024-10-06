<?php

namespace App\Service\FeeStrateg;

use App\Entity\VehiculeType;

interface FeeStrategyInterface
{
    public function calculate(float $basePrice , VehiculeType $vehicleType):float;

}