<?php

namespace App\Service;

use App\Entity\VehiculeType;
use App\Repository\SettingsRepository;
use App\Repository\AssociationFeesRepository;
use Exception;

class CalculationService
{
    private SettingsRepository $settingsRepository;
    private AssociationFeesRepository $associationFeeRepository;

    public function __construct(SettingsRepository $settingsRepository, AssociationFeesRepository $associationFeeRepository)
    {
        $this->settingsRepository = $settingsRepository;
        $this->associationFeeRepository = $associationFeeRepository;
    }

    /**
     *
     * @param float $basePrice
     * @param VehiculeType $vehicleType
     * @return array
     * @throws Exception
     */
    public function calculateTotalCost(float $basePrice, VehiculeType $vehicleType): array
    {
        $basicFeeRate = $vehicleType->getBasicFeeRate();
        $basicFee = $basePrice * $basicFeeRate;
        $basicFee = min(max($basicFee, $vehicleType->getBasicFeeMin()), $vehicleType->getBasicFeeMax());

        $specialFeeRate = $vehicleType->getSpecialFeeRate();
        $specialFee = $basePrice * $specialFeeRate;

        $associationFee = $this->getAssociationFee($basePrice);

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

    /**
     * @param float $basePrice
     * @return float
     * @throws Exception
     */
    private function getAssociationFee(float $basePrice): float
    {
        $fee = $this->associationFeeRepository->findFeeForPrice($basePrice);
        if ($fee === null) {
            throw new Exception('No association fee found for the given base price.');
        }
        return $fee->getAssociationFee();
    }
}
