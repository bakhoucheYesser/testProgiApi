<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class VehicleTypeDTO
{
    #[Assert\NotBlank(message: "Vehicle name cannot be blank.")]
    #[Assert\Length(min: 3, minMessage: "Vehicle name must be at least 3 characters long.")]
    private $name;

    #[Assert\NotBlank(message: "Basic fee minimum is required.")]
    #[Assert\Type(type: "numeric", message: "Basic fee minimum must be a number.")]
    #[Assert\Positive(message: "Basic fee minimum must be greater than zero.")]
    private $basicFeeMin;

    #[Assert\NotBlank(message: "Basic fee maximum is required.")]
    #[Assert\Type(type: "numeric", message: "Basic fee maximum must be a number.")]
    #[Assert\Positive(message: "Basic fee maximum must be greater than zero.")]
    private $basicFeeMax;

    #[Assert\NotBlank(message: "Basic fee percentage is required.")]
    #[Assert\Type(type: "numeric", message: "Basic fee percentage must be a number.")]
    #[Assert\Positive(message: "Basic fee percentage must be greater than zero.")]
    private $basicFeePercentage;

    #[Assert\NotBlank(message: "Special fee percentage is required.")]
    #[Assert\Type(type: "numeric", message: "Special fee percentage must be a number.")]
    #[Assert\Positive(message: "Special fee percentage must be greater than zero.")]
    private $specialFeePercentage;

    public function __construct($name, $basicFeeMin, $basicFeeMax, $basicFeePercentage, $specialFeePercentage)
    {
        $this->name = $name;
        $this->basicFeeMin = $basicFeeMin;
        $this->basicFeeMax = $basicFeeMax;
        $this->basicFeePercentage = $basicFeePercentage;
        $this->specialFeePercentage = $specialFeePercentage;
    }

    public function getName() { return $this->name; }
    public function getBasicFeeMin() {
        return $this->basicFeeMin;
    }
    public function getBasicFeeMax() {
        return $this->basicFeeMax;
    }
    public function getBasicFeePercentage()
    { return $this->basicFeePercentage;
    }
    public function getSpecialFeePercentage() {
        return $this->specialFeePercentage;
    }
}
