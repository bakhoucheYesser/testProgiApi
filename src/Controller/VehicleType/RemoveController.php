<?php

namespace App\Controller\VehicleType;

use App\Entity\VehiculeType;
use App\Helper\RequestHelper;
use App\Service\VehicleTypeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RemoveController extends AbstractController
{
    private $requestHelper;
    private $vehicleTypeService;

    public function __construct(RequestHelper $requestHelper, VehicleTypeService $vehicleTypeService)
    {
        $this->requestHelper = $requestHelper;
        $this->vehicleTypeService = $vehicleTypeService;
    }

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/api/vehicle-types/{id}', name: 'delete_vehicle_type', methods: ['DELETE'])]
    public function deleteVehicleType(int $id, EntityManagerInterface $em): JsonResponse
    {
        $vehicleType = $em->getRepository(VehiculeType::class)->find($id);
        if (!$vehicleType) {
            return $this->json(['error' => 'Vehicle type not found'], 404);
        }

        $em->remove($vehicleType);
        $em->flush();

        return $this->json(['status' => 'Vehicle type deleted successfully']);
    }

}