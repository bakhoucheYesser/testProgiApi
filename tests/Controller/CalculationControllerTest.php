<?php

namespace App\Tests\Controller;

use App\Controller\CalculationController;
use App\Entity\User;
use App\Entity\VehiculeType;
use App\Service\CalculationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CalculationControllerTest extends KernelTestCase
{
    private $calculationService;
    private $jwtManager;
    private $entityManager;
    private $validator;
    private $controller;

    protected function setUp(): void
    {
        // Boot Symfony's kernel to access the container
        self::bootKernel();

        // Mock the services
        $this->calculationService = $this->createMock(CalculationService::class);
        $this->jwtManager = $this->createMock(JWTTokenManagerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->validator = $this->createMock(ValidatorInterface::class);

        // Instantiate the controller and set the container
        $this->controller = new CalculationController($this->calculationService, $this->jwtManager);
        $this->controller->setContainer(static::getContainer());  // Use static::getContainer() for Symfony 5.3+
    }

    public function testSaveCalculationWithValidDataAndAuthenticatedUser()
    {
        $basePrice = 100;
        $vehicleTypeId = 1;

        // Mock request data
        $requestData = json_encode([
            'base_price' => $basePrice,
            'vehicle_type_id' => $vehicleTypeId
        ]);
        $request = Request::create('/api/calculate', 'POST', [], [], [], [], $requestData);

        // Mock the JWT token and user authentication
        $authToken = 'some_token';
        $request->headers->set('Authorization', 'Bearer ' . $authToken);

        $userEmail = 'test@example.com';
        $decodedToken = ['email' => $userEmail];
        $this->jwtManager->expects($this->once())
            ->method('parse')
            ->with($authToken)
            ->willReturn($decodedToken);

        // Mock the user
        $user = $this->createMock(User::class);
        $userRepository = $this->createMock(EntityRepository::class);
        $vehicleRepository = $this->createMock(EntityRepository::class);

        // Ensure that getRepository is returning the correct repository for both User and VehiculeType
        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnMap([
                [User::class, $userRepository],
                [VehiculeType::class, $vehicleRepository]
            ]);

        $userRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $userEmail])
            ->willReturn($user);

        // Mock validation - no validation errors
        $constraintViolationList = $this->createMock(ConstraintViolationListInterface::class);
        $constraintViolationList->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn($constraintViolationList);

        // Mock vehicle type
        $vehicleType = $this->createMock(VehiculeType::class);
        $vehicleRepository->expects($this->once())
            ->method('find')
            ->with($vehicleTypeId)
            ->willReturn($vehicleType);  // Return a valid vehicle type instead of null

        // Mock fee calculation
        $fees = [
            'basic_fee' => 10,
            'special_fee' => 20,
            'association_fee' => 5,
            'storage_fee' => 15,
            'total_cost' => 150
        ];
        $this->calculationService->expects($this->once())
            ->method('calculateTotalCost')
            ->with($basePrice, $vehicleType)
            ->willReturn($fees);

        // Mock the entity manager persist and flush for saving calculation
        $this->entityManager->expects($this->once())->method('persist');
        $this->entityManager->expects($this->once())->method('flush');

        // Call the controller method
        $response = $this->controller->saveCalculation($request, $this->entityManager, $this->validator);

        // Assert response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $responseData = json_decode($response->getContent(), true);

        // Assert the expected response structure
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('Calculation completed!', $responseData['status']);
        $this->assertTrue($responseData['saved']);
        $this->assertEquals($fees, $responseData['fees']);
    }

    public function testSaveCalculationWithInvalidVehicleType()
    {
        $basePrice = 100;
        $vehicleTypeId = 999; // Invalid vehicle type

        // Mock request data
        $requestData = json_encode([
            'base_price' => $basePrice,
            'vehicle_type_id' => $vehicleTypeId
        ]);
        $request = Request::create('/api/calculate', 'POST', [], [], [], [], $requestData);

        // Mock validation - no validation errors
        $constraintViolationList = $this->createMock(ConstraintViolationListInterface::class);
        $constraintViolationList->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $this->validator->expects($this->once())
            ->method('validate')
            ->willReturn($constraintViolationList);

        // Mock vehicle type repository to return null (invalid vehicle type)
        $vehicleRepository = $this->createMock(EntityRepository::class);
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(VehiculeType::class)
            ->willReturn($vehicleRepository);

        $vehicleRepository->expects($this->once())
            ->method('find')
            ->with($vehicleTypeId)
            ->willReturn(null);

        // Call the controller method
        $response = $this->controller->saveCalculation($request, $this->entityManager, $this->validator);

        // Assert response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(['error' => 'Invalid vehicle type ID.'], json_decode($response->getContent(), true));
    }
}
