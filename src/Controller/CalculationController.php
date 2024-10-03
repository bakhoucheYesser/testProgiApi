<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CalculationService;
use App\Repository\VehiculeTypeRepository;
use App\Helper\RequestHelper;
use App\DTO\CalculationInput;

class CalculationController extends AbstractController
{
    private $calculationService;
    private $vehicleTypeRepository;
    private $requestHelper;

    public function __construct(
        CalculationService $calculationService,
        VehiculeTypeRepository $vehicleTypeRepository,
        RequestHelper $requestHelper
    ) {
        $this->calculationService = $calculationService;
        $this->vehicleTypeRepository = $vehicleTypeRepository;
        $this->requestHelper = $requestHelper;
    }

    #[Route('/api/calculate', name: 'api_calculate', methods: ['POST'])]
    public function calculate(Request $request): JsonResponse
    {
        // Decode the JSON body of the request
        $inputData = json_decode($request->getContent(), true);

        // Instantiate the DTO with the request data
        $calculationInput = new CalculationInput($inputData['base_price'] ?? null, $inputData['vehicle_type_id'] ?? null);

        // Validate the DTO using the helper
        $errors = $this->requestHelper->validateDTO($calculationInput);
        if (count($errors) > 0) {
            return $this->requestHelper->formatValidationErrors($errors);
        }

        // Fetch the vehicle type from the database
        $vehicleType = $this->vehicleTypeRepository->find($calculationInput->getVehicleTypeId());
        if (!$vehicleType) {
            return $this->json([
                'error' => 'Invalid vehicle type ID.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Perform the calculation
        try {
            $result = $this->calculationService->calculateTotalCost($calculationInput->getBasePrice(), $vehicleType);
            return $this->requestHelper->createResponse($result);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'An error occurred during calculation: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}


