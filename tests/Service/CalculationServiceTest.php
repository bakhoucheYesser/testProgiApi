<?php

namespace App\Tests\Service;

use App\Entity\Settings;
use App\Entity\VehiculeType;
use App\Service\CalculationService;
use App\Service\FeeStrateg\AssociationFeeStrategy;
use App\Service\FeeStrateg\BasicFeeStrategy;
use App\Service\FeeStrateg\SpecialFeeStrategy;
use App\Repository\SettingsRepository;
use PHPUnit\Framework\TestCase;

class CalculationServiceTest extends TestCase
{
    private $settingsRepository;
    private $basicFeeStrategy;
    private $specialFeeStrategy;
    private $associationFeeStrategy;
    private $calculationService;

    protected function setUp(): void
    {
        $this->settingsRepository = $this->createMock(SettingsRepository::class);
        $this->basicFeeStrategy = $this->createMock(BasicFeeStrategy::class);
        $this->specialFeeStrategy = $this->createMock(SpecialFeeStrategy::class);
        $this->associationFeeStrategy = $this->createMock(AssociationFeeStrategy::class);

        $this->calculationService = new CalculationService(
            $this->settingsRepository,
            $this->basicFeeStrategy,
            $this->specialFeeStrategy,
            $this->associationFeeStrategy
        );
    }

    public function testCalculateTotalCost(): void
    {
        // Mock VehiculeType
        $vehicleType = $this->createMock(VehiculeType::class);

        // Mock strategies
        $this->basicFeeStrategy->method('calculate')->willReturn(50.0);
        $this->specialFeeStrategy->method('calculate')->willReturn(40.0);
        $this->associationFeeStrategy->method('calculate')->willReturn(20.0);

        // Mock storage fee
        $storageSetting = $this->createMock(Settings::class);
        $storageSetting->method('getValue')->willReturn(100.0);
        $this->settingsRepository->method('findOneBy')->willReturn($storageSetting);

        // Run the calculation
        $result = $this->calculationService->calculateTotalCost(1000.0, $vehicleType);

        // Assert the results
        $this->assertSame(1000.0, $result['base_price']);
        $this->assertSame(50.0, $result['basic_fee']);
        $this->assertSame(40.0, $result['special_fee']);
        $this->assertSame(20.0, $result['association_fee']);
        $this->assertSame(100.0, $result['storage_fee']);
        $this->assertSame(1210.0, $result['total_cost']);
    }
}
