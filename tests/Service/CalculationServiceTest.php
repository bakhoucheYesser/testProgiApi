<?php

namespace App\Tests\Service;

use App\Entity\VehiculeType;
use App\Service\CalculationService;
use App\Service\FeeStrateg\FeeStrategyInterface;
use PHPUnit\Framework\TestCase;

class CalculationServiceTest extends TestCase
{
    private $mockFeeStrategy1;
    private $mockFeeStrategy2;
    private $vehicleType;
    private $basePrice = 100.0;

    protected function setUp(): void
    {
        // Create mock for two FeeStrategyInterface implementations
        $this->mockFeeStrategy1 = $this->getMockBuilder(FeeStrategyInterface::class)
            ->setMockClassName('MockFeeStrategy1')
            ->getMock();

        $this->mockFeeStrategy2 = $this->getMockBuilder(FeeStrategyInterface::class)
            ->setMockClassName('MockFeeStrategy2')
            ->getMock();

        // Set up the mock vehicle type
        $this->vehicleType = $this->createMock(VehiculeType::class);
    }

    public function testCalculateTotalCost()
    {
        // Set expectations for the mock fee strategies
        $this->mockFeeStrategy1
            ->expects($this->once())
            ->method('calculate')
            ->with($this->basePrice, $this->vehicleType)
            ->willReturn(10.0);  // Example fee value for strategy 1

        $this->mockFeeStrategy2
            ->expects($this->once())
            ->method('calculate')
            ->with($this->basePrice, $this->vehicleType)
            ->willReturn(20.0);  // Example fee value for strategy 2

        // Initialize the CalculationService with the mock strategies
        $calculationService = new CalculationService([$this->mockFeeStrategy1, $this->mockFeeStrategy2]);

        // Execute the calculateTotalCost method
        $fees = $calculationService->calculateTotalCost($this->basePrice, $this->vehicleType);

        // Assert that the fees array contains the correct base price and calculated fees
        $this->assertArrayHasKey('base_price', $fees);
        $this->assertEquals(100.0, $fees['base_price']);

        // Assert that the calculated fees are present with the correct class names
        $this->assertArrayHasKey('mockfeestrategy1', $fees);
        $this->assertEquals(10.0, $fees['mockfeestrategy1']);

        $this->assertArrayHasKey('mockfeestrategy2', $fees);
        $this->assertEquals(20.0, $fees['mockfeestrategy2']);

        // Assert total cost
        $this->assertEquals(130.0, $fees['total_cost']); // 100 base + 10 + 20
    }

    public function testCalculateTotalCostWithNoStrategies()
    {
        // Test the case where no strategies are provided
        $calculationService = new CalculationService([]);

        // Execute the calculateTotalCost method
        $fees = $calculationService->calculateTotalCost($this->basePrice, $this->vehicleType);

        // Assert that the fees array contains only the base price and total cost
        $this->assertArrayHasKey('base_price', $fees);
        $this->assertEquals(100.0, $fees['base_price']);

        $this->assertArrayHasKey('total_cost', $fees);
        $this->assertEquals(100.0, $fees['total_cost']); // No additional fees
    }
}
