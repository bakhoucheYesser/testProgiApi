<?php

namespace App\Service\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class UserService
{
    private JWTTokenManagerInterface $jwtManager;
    private EntityManagerInterface $em;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EntityManagerInterface $em,
        private readonly LoggerInterface $logger
    )
    {
        $this->jwtManager = $jwtManager;
        $this->em = $em;
    }

    /**
     * Retrieves a User entity from the JWT token.
     *
     * @param string|null $authHeader The Authorization header value.
     * @return User|null
     */
    public function getUserFromToken(?string $authHeader): ?User
    {
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return null;
        }

        $authToken = $matches[1];

        try {
            $decodedToken = $this->jwtManager->parse($authToken);
            $userEmail = $decodedToken['email'] ?? null;

            if ($userEmail) {
                return $this->em->getRepository(User::class)->findOneBy(['email' => $userEmail]);
            }
        } catch (AuthenticationException $e) {
            return null;
        }

        return null;
    }

    /**
     * @return array
     */
    public function getAllUsers(): array
    {
        return $this->em->getRepository(User::class)->findAll();
    }

    /**
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function removeUser(int $id): bool
    {
        $user = $this->em->getRepository(User::class)->find($id);
        if (!$user) {
            throw new \Exception("User with ID $id not found.");
        }

        try {
            $this->em->remove($user);
            $this->em->flush();
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Error removing user: " . $e->getMessage());
        }
    }

    public function addUser(User $user): bool
    {
      $this->logger->info("Add user: " . $user->getEmail());
    }

}
