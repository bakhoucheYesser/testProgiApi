<?php

namespace App\Controller\User;

use App\Helper\RequestHelper;
use App\Service\User\UserService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    private  $userService;
    private $requestHelper;
    private $logger;

    public function __construct(UserService $userService ,
                                RequestHelper $requestHelper ,
                                LoggerInterface $logger
    )
    {
        $this->userService =$userService;
        $this->requestHelper = $requestHelper;
        $this->logger = $logger;
    }


    #[Route('/api/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        try {
            $users = $this->userService->getAllUsers();
            return $this->requestHelper->createResponse
            (
                $users,
                true,
                200,
                ['user']
            );
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch users: ' . $e->getMessage());
            return $this->requestHelper->createResponse
            (
                ['error' => 'Failed to retrieve users'],
                false,
                500
            );
        }
    }




}


