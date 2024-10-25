<?php

namespace App\Controller;

use App\DTO\CalculationInput;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Calculations;
use App\Entity\VehiculeType;
use App\Service\CalculationService;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CalculationController extends AbstractController
{
    private CalculationService $calculationService;
    private JWTTokenManagerInterface $jwtManager;


    public function __construct(CalculationService $calculationService, JWTTokenManagerInterface $jwtManager)
    {
        $this->calculationService = $calculationService;
        $this->jwtManager = $jwtManager;
    }

    /**
     * @throws \ReflectionException
     */
    #[Route('/api/calculate', name: 'save_calculation', methods: ['POST'])]
    public function saveCalculation(Request $request, EntityManagerInterface $em , ValidatorInterface $validator
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $calculationInput = new CalculationInput(
            $data['base_price'] ?? 0,
            $data['vehicle_type_id'] ?? 0
        );

        $errors = $validator->validate($calculationInput);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $vehicleType = $em->getRepository(VehiculeType::class)->find($calculationInput->getVehicleTypeId());
        if (!$vehicleType) {
            return $this->json(['error' => 'Invalid vehicle type ID.'], Response::HTTP_BAD_REQUEST);
        }

        $fees = $this->calculationService->calculateTotalCost($calculationInput->getBasePrice(), $vehicleType);

        $authHeader = $request->headers->get('Authorization');
        $user = null;

        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $authToken = $matches[1];

            try {
                $decodedToken = $this->jwtManager->parse($authToken);
                $userEmail = $decodedToken['email'] ?? null;

                if ($userEmail) {
                    $user = $em->getRepository(User::class)->findOneBy(['email' => $userEmail]);
                }
            } catch (AuthenticationException $e) {
            }
        }

        if ($user instanceof User) {

            $calculation = new Calculations();
            $calculation->setBasePrice($calculationInput->getBasePrice());
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
                'saved' => true
            ]);
        } else {

            return $this->json([
                'status' => 'Calculation completed!',
                'fees' => $fees,
                'saved' => false
            ]);
        }
    }

}
