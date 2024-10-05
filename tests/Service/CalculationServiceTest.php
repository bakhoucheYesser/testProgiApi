<?php

namespace App\Tests\Service;

use App\Entity\AssociationFees;
use App\Entity\Settings;
use App\Entity\VehiculeType;
use App\Repository\SettingsRepository;
use App\Repository\AssociationFeesRepository;
use App\Service\CalculationService;
use PHPUnit\Framework\TestCase;

class CalculationServiceTest extends TestCase
{
    private $settingsRepository;
    private $associationFeeRepository;
    private $calculationService;

    protected function setUp(): void
    {
        $this->settingsRepository = $this->createMock(SettingsRepository::class);
        $this->associationFeeRepository = $this->createMock(AssociationFeesRepository::class);
        $this->calculationService = new CalculationService($this->settingsRepository, $this->associationFeeRepository);
    }

    public function testCalculateTotalCost(): void
    {
        // Mock VehiculeType
        $vehicleType = $this->createMock(VehiculeType::class);
        $vehicleType->method('getBasicFeeRate')->willReturn(0.10);  // Return float
        $vehicleType->method('getBasicFeeMin')->willReturn(10.0);   // Return float, not int
        $vehicleType->method('getBasicFeeMax')->willReturn(50.0);   // Return float, not int
        $vehicleType->method('getSpecialFeeRate')->willReturn(0.04); // Return float

        // Mock AssociationFee
        $associationFee = $this->createMock(AssociationFees::class);
        $associationFee->method('getAssociationFee')->willReturn(20.0); // Return float

        // Mock repositories
        $this->associationFeeRepository->method('findFeeForPrice')->willReturn($associationFee);

        $storageSetting = $this->createMock(Settings::class);
        $storageSetting->method('getValue')->willReturn(100.0); // Return float
        $this->settingsRepository->method('findOneBy')->willReturn($storageSetting);

        $result = $this->calculationService->calculateTotalCost(1000.0, $vehicleType); // Use float for price

        $this->assertSame(1000.0, $result['base_price']);
        $this->assertSame(50.0, $result['basic_fee']);
        $this->assertSame(40.0, $result['special_fee']);
        $this->assertSame(20.0, $result['association_fee']);
        $this->assertSame(100.0, $result['storage_fee']);
        $this->assertSame(1210.0, $result['total_cost']);
    }
}
