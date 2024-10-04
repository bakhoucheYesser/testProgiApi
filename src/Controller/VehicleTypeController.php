<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\DTO\VehicleTypeDTO;
use App\Helper\RequestHelper;
use App\Service\VehicleTypeService;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VehicleTypeController extends AbstractController
{
    private $requestHelper;
    private $vehicleTypeService;

    public function __construct(RequestHelper $requestHelper, VehicleTypeService $vehicleTypeService)
    {
        $this->requestHelper = $requestHelper;
        $this->vehicleTypeService = $vehicleTypeService;
    }


    #[Route('/api/vehicle-types', name: 'get_vehicle_types', methods: ['GET'])]
    public function getVehicleTypes(): JsonResponse
    {
        $vehicleTypes = $this->vehicleTypeService->getAllVehicleTypes();
        return $this->json($vehicleTypes, 200, [], ['groups' => ['vehicle']]);
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
            $data['basic_fee_percentage'],
            $data['special_fee_percentage']
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

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/api/vehicle-types/{id}', name: 'update_vehicle_type', methods: ['PUT'])]
    public function updateVehicleType($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);


        $dto = new VehicleTypeDTO(
            $data['name'],
            $data['basic_fee_min'],
            $data['basic_fee_max'],
            $data['basic_fee_percentage'],
            $data['special_fee_percentage']
        );

        $errors = $this->requestHelper->validateDTO($dto);
        if (count($errors) > 0) {
            return $this->requestHelper->formatValidationErrors($errors);
        }

        $vehicleType = $this->vehicleTypeService->updateVehicleType($id, $dto);
        if (!$vehicleType) {
            return $this->json(['status' => 'Vehicle type not found'], 404);
        }

        return $this->json([
            'status' => 'Vehicle type updated!',
            'vehicleType' => $vehicleType
        ]);
    }
}
