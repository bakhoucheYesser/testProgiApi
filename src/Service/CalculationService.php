<?php
namespace App\Service;


use App\Entity\VehiculeType;
use App\Service\FeeStrateg\FeeStrategyInterface;
use InvalidArgumentException;
use ReflectionException;

class CalculationService
{
    private iterable $feeStrategies;

    // The constructor directly receives the tagged strategies from the service container
    public function __construct(iterable $feeStrategies)
    {
        $this->feeStrategies = $feeStrategies;
    }

    /**
     * @throws ReflectionException
     */
    public function calculateTotalCost(float $basePrice, VehiculeType $vehicleType): array
    {
        $fees = ['base_price' => $basePrice];
        $totalCost = $basePrice;

        // Iterate over each injected strategy and calculate the fee
        foreach ($this->feeStrategies as $strategy) {
            // The fee type will depend on the actual class, this logic can vary
            $feeType = (new \ReflectionClass($strategy))->getShortName();
            $fee = $strategy->calculate($basePrice, $vehicleType);
            $fees[strtolower($feeType)] = $fee;
            $totalCost += $fee;
        }


        $fees['total_cost'] = $totalCost;

        return $fees;
    }
}
