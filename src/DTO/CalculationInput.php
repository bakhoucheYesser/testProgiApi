<?php


namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CalculationInput
{
    #[Assert\NotBlank(message: "Base price cannot be blank.")]
    #[Assert\Type(type: "numeric", message: "Base price must be a number.")]
    #[Assert\Positive(message: "Base price must be greater than zero.")]
    private $basePrice;

    #[Assert\NotBlank(message: "Vehicle type ID cannot be blank.")]
    #[Assert\Type(type: "numeric", message: "Vehicle type ID must be a number.")]
    #[Assert\Positive(message: "Vehicle type ID must be greater than zero.")]
    private $vehicleTypeId;

    public function __construct($basePrice, $vehicleTypeId)
    {
        $this->basePrice = $basePrice;
        $this->vehicleTypeId = $vehicleTypeId;
    }

    public function getBasePrice()
    {
        return $this->basePrice;
    }

    public function getVehicleTypeId()
    {
        return $this->vehicleTypeId;
    }
}
