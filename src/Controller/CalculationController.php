<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Calculations;
use App\Entity\VehiculeType;
use App\Service\CalculationService;

class CalculationController extends AbstractController
{
    private $calculationService;

    public function __construct(CalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    /**
     * @throws \Exception
     */
    #[Route('/api/calculate', name: 'save_calculation', methods: ['POST'])]
    public function saveCalculation(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Decode the incoming request
        $data = json_decode($request->getContent(), true);
        $basePrice = $data['base_price'] ?? null;
        $vehicleTypeId = $data['vehicle_type_id'] ?? null;

        // Validate input
        if (!$basePrice || !$vehicleTypeId) {
            return $this->json(['error' => 'Invalid input. Base price and vehicle type are required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Fetch the vehicle type from the database
        $vehicleType = $em->getRepository(VehiculeType::class)->find($vehicleTypeId);
        if (!$vehicleType) {
            return $this->json(['error' => 'Invalid vehicle type ID.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Calculate total cost using CalculationService
        $fees = $this->calculationService->calculateTotalCost($basePrice, $vehicleType);

        // Check if the user is authenticated
        $user = $this->getUser();

        if ($user instanceof User) {
            // If the user is authenticated, save the calculation to the database
            $calculation = new Calculations();
            $calculation->setBasePrice($basePrice);
            $calculation->setVehicleType($vehicleType);
            $calculation->setBasicFee($fees['basic_fee']);
            $calculation->setSpecialFee($fees['special_fee']);
            $calculation->setAssociationFee($fees['association_fee']);
            $calculation->setStorageFee($fees['storage_fee']);
            $calculation->setTotalPrice($fees['total_cost']);
            $calculation->setUser($user);  // Associate with authenticated user

            // Persist and save the calculation
            $em->persist($calculation);
            $em->flush();
        }

        return $this->json([
            'status' => 'Calculation completed!',
            'fees' => $fees,
            'saved' => (bool)$user  // Indicate if calculation was saved
        ]);
    }
}
