<?php

namespace App\Helper;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Serializer\SerializerInterface;

class RequestHelper
{
    private $validator;
    private $serializer;

    public function __construct(ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    /**
     * Validate the given DTO.
     *
     * @param object $dto
     * @return ConstraintViolationListInterface
     */
    public function validateDTO(object $dto): ConstraintViolationListInterface
    {
        return $this->validator->validate($dto);
    }

    /**
     * Format validation errors into a JSON response.
     *
     * @param ConstraintViolationListInterface $errors
     * @return JsonResponse
     */
    public function formatValidationErrors(ConstraintViolationListInterface $errors): JsonResponse
    {
        $formattedErrors = [];
        foreach ($errors as $error) {
            $formattedErrors[] = $error->getPropertyPath() . ': ' . $error->getMessage();
        }

        return new JsonResponse([
            'error' => 'Invalid input.',
            'details' => $formattedErrors,
        ], JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * Create a standardized JSON response.
     *
     * @param array $data
     * @param bool $success
     * @param int $status
     * @return JsonResponse
     */
    public function createResponse(array $data, bool $success = true, int $status = 200): JsonResponse
    {
        return new JsonResponse([
            'success' => $success,
            'data' => $data,
        ], $status);
    }

    /**
     * Serialize an object to JSON.
     *
     * @param mixed $data
     * @return string
     */
    public function serialize($data): string
    {
        return $this->serializer->serialize($data, 'json');
    }
}
