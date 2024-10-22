<?php
namespace App\Service;

use App\Entity\Calculations;
use App\Entity\User;
use App\Entity\VehiculeType;
use App\Service\FeeStrateg\FeeStrategyInterface;
use InvalidArgumentException;

class CalculationService
{
    private array $feeStrategies;

    public function __construct(iterable $feeStrategies)
    {
        $this->feeStrategies = $this->categorizeFeeStrategies($feeStrategies);
    }

    public function calculateTotalCost(float $basePrice, VehiculeType $vehicleType): array
    {
        $fees = ['base_price' => $basePrice];
        $totalCost = $basePrice;

        foreach ($this->feeStrategies as $feeType => $strategy) {
            $fee = $strategy->calculate($basePrice, $vehicleType);
            $fees[$feeType] = $fee;
            $totalCost += $fee;
        }

        $fees['total_cost'] = $totalCost;

        return $fees;
    }

    public function createCalculation(array $fees, VehiculeType $vehicleType, User $user): Calculations
    {
        $this->validateFees($fees);

        $calculation = new Calculations();
        $calculation->setBasePrice($fees['base_price'])
            ->setBasicFee($fees['basic_fee'])
            ->setSpecialFee($fees['special_fee'])
            ->setAssociationFee($fees['association_fee'])
            ->setStorageFee($fees['storage_fee'])
            ->setTotalPrice($fees['total_cost'])
            ->setVehicleType($vehicleType)
            ->setUser($user);

        return $calculation;
    }

    private function categorizeFeeStrategies(iterable $strategies): array
    {
        $categorized = [];
        foreach ($strategies as $strategy) {
            if (!$strategy instanceof FeeStrategyInterface) {
                throw new InvalidArgumentException(sprintf('Strategy must implement FeeStrategyInterface. %s given.', get_class($strategy)));
            }
            $categorized[$this->getFeeTypeFromStrategy($strategy)] = $strategy;
        }
        return $categorized;
    }

    private function getFeeTypeFromStrategy(FeeStrategyInterface $strategy): string
    {
        $className = get_class($strategy);
        $baseName = substr($className, strrpos($className, '\\') + 1);
        return strtolower(str_replace('FeeStrategy', '', $baseName)) . '_fee';
    }

    private function validateFees(array $fees): void
    {
        $requiredFees = ['base_price', 'basic_fee', 'special_fee', 'association_fee', 'storage_fee', 'total_cost'];
        $missingFees = array_diff($requiredFees, array_keys($fees));

        if (!empty($missingFees)) {
            throw new InvalidArgumentException('Missing expected fee data: ' . implode(', ', $missingFees));
        }
    }
}
