<?php

namespace App\Tests\Service;

use App\DTO\VehicleTypeDTO;
use App\Entity\VehiculeType;
use App\Service\VehicleTypeService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class VehicleTypeServiceTest extends TestCase
{
    private $em;
    private $repository;
    private $vehicleTypeService;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->repository = $this->createMock(EntityRepository::class);

        $this->em->method('getRepository')
            ->willReturn($this->repository);

        $this->vehicleTypeService = new VehicleTypeService($this->em);
    }

    public function testCreateVehicleType(): void
    {
        $dto = new VehicleTypeDTO(
            'Luxury',
            50.0,
            200.0,
            0.10,
            0.04
        );


        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(VehiculeType::class));

        $this->em->expects($this->once())
            ->method('flush');

        $vehicleType = $this->vehicleTypeService->createVehicleType($dto);

        $this->assertInstanceOf(VehiculeType::class, $vehicleType);
        $this->assertSame('Luxury', $vehicleType->getName());
        $this->assertSame(50.0, $vehicleType->getBasicFeeMin());
        $this->assertSame(200.0, $vehicleType->getBasicFeeMax());
        $this->assertSame(0.10, $vehicleType->getBasicFeeRate());
        $this->assertSame(0.04, $vehicleType->getSpecialFeeRate());
    }

    public function testUpdateVehicleType(): void
    {
        $dto = new VehicleTypeDTO('Common', 10.0, 50.0, 0.05, 0.02);


        $existingVehicleType = $this->createMock(VehiculeType::class);


        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($existingVehicleType);


        $existingVehicleType->expects($this->once())->method('setName')->with('Common');
        $existingVehicleType->expects($this->once())->method('setBasicFeeMin')->with(10.0);
        $existingVehicleType->expects($this->once())->method('setBasicFeeMax')->with(50.0);
        $existingVehicleType->expects($this->once())->method('setBasicFeeRate')->with(0.05);
        $existingVehicleType->expects($this->once())->method('setSpecialFeeRate')->with(0.02);


        $this->em->expects($this->once())->method('flush');

        $updatedVehicleType = $this->vehicleTypeService->updateVehicleType(1, $dto);

        $this->assertSame($existingVehicleType, $updatedVehicleType);
    }

    public function testUpdateVehicleTypeReturnsNullIfNotFound(): void
    {

        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $dto = new VehicleTypeDTO(
            'Common',
            10.0,
            50.0,
            0.05,
            0.02
        );

        $updatedVehicleType = $this->vehicleTypeService->updateVehicleType(1, $dto);


        $this->assertNull($updatedVehicleType);
    }

    public function testGetAllVehicleTypes(): void
    {

        $vehicleTypes = [
            $this->createMock(VehiculeType::class),
            $this->createMock(VehiculeType::class)
        ];


        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn($vehicleTypes);

        $result = $this->vehicleTypeService->getAllVehicleTypes();

        $this->assertCount(2, $result);
        $this->assertSame($vehicleTypes, $result);
    }

    public function testFindVehicleTypeById(): void
    {
        $vehicleType = $this->createMock(VehiculeType::class);


        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($vehicleType);

        $result = $this->vehicleTypeService->findVehicleTypeById(1);

        $this->assertSame($vehicleType, $result);
    }

    public function testFindVehicleTypeByIdReturnsNullIfNotFound(): void
    {
        // Mock the repository to return null (vehicle type not found)
        $this->repository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn(null);

        $result = $this->vehicleTypeService->findVehicleTypeById(1);

        $this->assertNull($result);
    }
}
