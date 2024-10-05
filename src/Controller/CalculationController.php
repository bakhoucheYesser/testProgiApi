<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Calculations;
use App\Entity\VehiculeType;
use App\Service\CalculationService;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class CalculationController extends AbstractController
{
    private CalculationService $calculationService;
    private JWTTokenManagerInterface $jwtManager;


    public function __construct(CalculationService $calculationService, JWTTokenManagerInterface $jwtManager)
    {
        $this->calculationService = $calculationService;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/calculate', name: 'save_calculation', methods: ['POST'])]
    public function saveCalculation(Request $request, EntityManagerInterface $em): JsonResponse
    {
        // Decode the incoming request
        $data = json_decode($request->getContent(), true);
        $basePrice = $data['base_price'] ?? null;
        $vehicleTypeId = $data['vehicle_type_id'] ?? null;

        if (!$basePrice || !$vehicleTypeId) {
            return $this->json(['error' => 'Invalid input. Base price and vehicle type are required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Fetch vehicle type (your existing logic)
        $vehicleType = $em->getRepository(VehiculeType::class)->find($vehicleTypeId);
        if (!$vehicleType) {
            return $this->json(['error' => 'Invalid vehicle type ID.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Calculate the fees (your existing logic)
        $fees = $this->calculationService->calculateTotalCost($basePrice, $vehicleType);

        // Check for the JWT token manually in the Authorization header
        $authHeader = $request->headers->get('Authorization');
        $authToken = null;
        $user = null;

        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $authToken = $matches[1];

            // Try to decode the token and fetch the user
            try {
                $decodedToken = $this->jwtManager->parse($authToken);
                $userEmail = $decodedToken['email'] ?? null;

                // Fetch user by email
                if ($userEmail) {
                    $user = $em->getRepository(User::class)->findOneBy(['email' => $userEmail]);
                }
            } catch (AuthenticationException $e) {
                // Token is invalid or expired, no authenticated user
            }
        }

        if ($user instanceof User) {
            // Authenticated user, save the calculation
            $calculation = new Calculations();
            $calculation->setBasePrice($basePrice);
            $calculation->setVehicleType($vehicleType);
            $calculation->setBasicFee($fees['basic_fee']);
            $calculation->setSpecialFee($fees['special_fee']);
            $calculation->setAssociationFee($fees['association_fee']);
            $calculation->setStorageFee($fees['storage_fee']);
            $calculation->setTotalPrice($fees['total_cost']);
            $calculation->setUser($user);

            $em->persist($calculation);
            $em->flush();

            return $this->json([
                'status' => 'Calculation completed!',
                'fees' => $fees,
                'saved' => true  // Saved for authenticated user
            ]);
        } else {
            // No authenticated user, return the calculation result without saving
            return $this->json([
                'status' => 'Calculation completed!',
                'fees' => $fees,
                'saved' => false  // Not saved for guest users
            ]);
        }
    }

}
