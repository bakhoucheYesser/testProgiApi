<?php

namespace App\Controller\User;

use App\Helper\RequestHelper;
use App\Service\User\UserService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class RemoveController extends AbstractController
{
    private  $userService;
    private $requestHelper;
    private $logger;

    public function __construct(UserService $userService ,
                                RequestHelper $requestHelper
    )
    {
        $this->userService =$userService;
        $this->requestHelper = $requestHelper;
    }

    #[Route('/api/users/{id}/delete', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id): JsonResponse
    {
        try {
            $this->userService->removeUser($id);
            return $this->requestHelper->createResponse(
                ['message' => 'User removed successfully.']
            );
        } catch (\Exception $e) {
            return $this->requestHelper->createResponse
            (
                ['error' => $e->getMessage()],
                false,
                400
            );
        }
    }


}