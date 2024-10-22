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
     *
     * @param object $dto
     * @return ConstraintViolationListInterface
     */
    public function validateDTO(object $dto): ConstraintViolationListInterface
    {
        return $this->validator->validate($dto);
    }

    /**
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

        return new JsonResponse
        (
            [
            'error' => 'Invalid input.',
            'details' => $formattedErrors,
            ],
            JsonResponse::HTTP_BAD_REQUEST
        );
    }

    /**
     * @param array $data
     * @param bool $success
     * @param int $status
     * @param array $groups
     * @return JsonResponse
     */
    public function createResponse(array $data, bool $success = true, int $status = 200, array $groups = []): JsonResponse
    {
        if (!empty($groups)) {
            $data = json_decode($this->serializer->serialize($data, 'json', ['groups' => $groups]), true);
        }

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
