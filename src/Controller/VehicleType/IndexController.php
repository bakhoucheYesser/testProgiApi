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



}
