<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CalculationInput
{
    #[Assert\NotBlank(message: "Base price cannot be blank.")]
    #[Assert\Type(type: "float", message: "Base price must be a number.")]
    #[Assert\Positive(message: "Base price must be greater than zero.")]
    private float $basePrice;

    #[Assert\NotBlank(message: "Vehicle type ID cannot be blank.")]
    #[Assert\Type(type: "int", message: "Vehicle type ID must be a number.")]
    #[Assert\Positive(message: "Vehicle type ID must be greater than zero.")]
    private int $vehicleTypeId;

    public function __construct(float $basePrice, int $vehicleTypeId)
    {
        $this->basePrice = $basePrice;
        $this->vehicleTypeId = $vehicleTypeId;
    }

    public function getBasePrice(): float
    {
        return $this->basePrice;
    }

    public function getVehicleTypeId(): int
    {
        return $this->vehicleTypeId;
    }
}
