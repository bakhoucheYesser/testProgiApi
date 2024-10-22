<?php

namespace App\Controller\VehicleType;

use App\DTO\VehicleTypeDTO;
use App\Helper\RequestHelper;
use App\Service\VehicleTypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CreateController extends AbstractController
{
    private RequestHelper $requestHelper;
    private VehicleTypeService $vehicleTypeService;

    public function __construct(RequestHelper $requestHelper, VehicleTypeService $vehicleTypeService)
    {
        $this->requestHelper = $requestHelper;
        $this->vehicleTypeService = $vehicleTypeService;
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/api/vehicle-types', name: 'create_vehicle_type', methods: ['POST'])]
    public function createVehicleType(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Create DTO and validate it
        $dto = new VehicleTypeDTO(
            $data['name'],
            $data['basic_fee_min'],
            $data['basic_fee_max'],
            $data['basic_fee_rate'],
            $data['special_fee_rate']
        );

        $errors = $this->requestHelper->validateDTO($dto);
        if (count($errors) > 0) {
            return $this->requestHelper->formatValidationErrors($errors);
        }

        $vehicleType = $this->vehicleTypeService->createVehicleType($dto);

        return $this->json([
            'status' => 'Vehicle type created!',
            'vehicleType' => $vehicleType
        ]);
    }

}