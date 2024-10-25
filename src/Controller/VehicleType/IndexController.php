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

class IndexController extends AbstractController
{

    private VehicleTypeService $vehicleTypeService;

    public function __construct(VehicleTypeService $vehicleTypeService)
    {
        $this->vehicleTypeService = $vehicleTypeService;
    }

    #[Route('/api/vehicle-types', name: 'get_vehicle_types', methods: ['GET'])]
    public function getVehicleTypes(): JsonResponse
    {

        try {
            $vehicleTypes = $this->vehicleTypeService->getAllVehicleTypes();
            if (!$vehicleTypes) {
                return $this->json(['message' => 'No Vehicle types found'] , 404);
            }
            return $this->json($vehicleTypes, 200, [], ['groups' => ['vehicle']]);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
//        $vehicleTypes = $this->vehicleTypeService->getAllVehicleTypes();
//        return $this->json($vehicleTypes, 200, [], ['groups' => ['vehicle']]);
    }
}
