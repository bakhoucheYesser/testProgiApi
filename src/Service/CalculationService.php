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
     * Calculate the total cost for a vehicle based on its type and base price.
     *
     * @param float $basePrice
     * @param VehiculeType $vehicleType
     * @return array
     * @throws Exception
     */
    public function calculateTotalCost(float $basePrice, VehiculeType $vehicleType): array
    {
        // Basic Buyer Fee
        $basicFeeRate = $vehicleType->getBasicFeeRate();
        $basicFee = $basePrice * $basicFeeRate;
        $basicFee = min(max($basicFee, $vehicleType->getBasicFeeMin()), $vehicleType->getBasicFeeMax());

        // Special Seller Fee
        $specialFeeRate = $vehicleType->getSpecialFeeRate();
        $specialFee = $basePrice * $specialFeeRate;

        // Association Fee (dynamically fetched from the association_fees table)
        $associationFee = $this->getAssociationFee($basePrice);

        // Storage Fee (fetched from the settings table)
        $storageFee = $this->settingsRepository->findOneBy(['setting_key' => 'storage_fee'])->getValue();

        // Total Calculation
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
     * @param float $basePrice The base price of the vehicle.
     * @return float The association fee based on the price range.
     * @throws Exception If no association fee is found for the given base price.
     */
    private function getAssociationFee(float $basePrice): float
    {
        // Query the association fee for the given base price
        $fee = $this->associationFeeRepository->findFeeForPrice($basePrice);

        if ($fee === null) {
            throw new Exception('No association fee found for the given base price.');
        }

        return $fee->getAssociationFee();
    }
}
