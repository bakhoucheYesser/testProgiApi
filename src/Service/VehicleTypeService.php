<?php

namespace App\Service;

use App\Entity\VehiculeType;
use Doctrine\ORM\EntityManagerInterface;
use App\DTO\VehicleTypeDTO;

class VehicleTypeService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Create a new vehicle type.
     */
    public function createVehicleType(VehicleTypeDTO $dto): VehiculeType
    {
        $vehicleType = new VehiculeType();
        $vehicleType->setName($dto->getName());
        $vehicleType->setBasicFeeMin($dto->getBasicFeeMin());
        $vehicleType->setBasicFeeMax($dto->getBasicFeeMax());
        $vehicleType->setBasicFeeRate($dto->getBasicFeePercentage());
        $vehicleType->setSpecialFeeRate($dto->getSpecialFeePercentage());

        $this->em->persist($vehicleType);
        $this->em->flush();

        return $vehicleType;
    }

    /**
     * Update an existing vehicle type.
     */
    public function updateVehicleType(int $id, VehicleTypeDTO $dto): ?VehiculeType
    {
        $vehicleType = $this->em->getRepository(VehiculeType::class)->find($id);

        if (!$vehicleType) {
            return null;
        }

        $vehicleType->setName($dto->getName());
        $vehicleType->setBasicFeeMin($dto->getBasicFeeMin());
        $vehicleType->setBasicFeeMax($dto->getBasicFeeMax());
        $vehicleType->setSpecialFeeRate($dto->getBasicFeePercentage());
        $vehicleType->setSpecialFeeRate($dto->getSpecialFeePercentage());

        $this->em->flush();

        return $vehicleType;
    }

    /**
     * Get all vehicle types.
     */
    public function getAllVehicleTypes(): array
    {
        return $this->em->getRepository(VehiculeType::class)->findAll();
    }

    /**
     * Find a vehicle type by ID.
     */
    public function findVehicleTypeById(int $id): ?VehiculeType
    {
        return $this->em->getRepository(VehiculeType::class)->find($id);
    }
}

