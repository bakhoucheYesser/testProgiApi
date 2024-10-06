<?php

namespace App\Service;

use App\Entity\VehiculeType;
use App\Repository\SettingsRepository;
use App\Service\FeeStrateg\AssociationFeeStrategy;
use App\Service\FeeStrateg\BasicFeeStrategy;
use App\Service\FeeStrateg\SpecialFeeStrategy;


class CalculationService
{
    private SettingsRepository $settingsRepository;
    private BasicFeeStrategy $basicFeeStrategy;
    private SpecialFeeStrategy $specialFeeStrategy;
    private AssociationFeeStrategy $associationFeeStrategy;

    public function __construct(
        SettingsRepository $settingsRepository,
        BasicFeeStrategy $basicFeeStrategy,
        SpecialFeeStrategy $specialFeeStrategy,
        AssociationFeeStrategy $associationFeeStrategy
    ) {
        $this->settingsRepository = $settingsRepository;
        $this->basicFeeStrategy = $basicFeeStrategy;
        $this->specialFeeStrategy = $specialFeeStrategy;
        $this->associationFeeStrategy = $associationFeeStrategy;
    }

    /**
     * @throws \Exception
     */
    public function calculateTotalCost(float $basePrice, VehiculeType $vehicleType): array
    {
        $basicFee = $this->basicFeeStrategy->calculate($basePrice, $vehicleType);
        $specialFee = $this->specialFeeStrategy->calculate($basePrice, $vehicleType);
        $associationFee = $this->associationFeeStrategy->calculate($basePrice, $vehicleType);

        $storageFee = $this->settingsRepository->findOneBy(['setting_key' => 'storage_fee'])->getValue();
        $totalCost = $basePrice + $basicFee + $specialFee + $associationFee + $storageFee;

        return [
            'base_price' => $basePrice,
            'basic_fee' => $basicFee,
            'special_fee' => $specialFee,
            'association_fee' => $associationFee,
            'storage_fee' => $storageFee,
            'total_cost' => $totalCost,
        ];
    }
}

