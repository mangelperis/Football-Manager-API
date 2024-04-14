<?php

namespace App\Domain\Port;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ResponseHandlerInterface
{
    /**
     * Creates a JSON response with a message and status code.
     *
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function createResponse(string $message, int $statusCode): JsonResponse;

    /**
     * Creates a JSON response for a successful creation of an element.
     *
     * @param array $data
     * @param string $type
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    public function createSuccessResponse(array $data, string $type, int $statusCode, array $headers): JsonResponse;

    /**
     * Creates a JSON response for an error.
     *
     * @param string $message
     * @param int $statusCode
     * @param array|null $errors
     * @param array $headers
     * @return JsonResponse
     */
    public function returnErrorResponse(string $message, int $statusCode, ?array $errors, array $headers): JsonResponse;

    /**
     * Creates a JSON response from a DTO array.
     *
     * @param array $DTO
     * @return JsonResponse
     */
    public function createDtoResponse(array $DTO): JsonResponse;

    /**
     * Creates a JSON response for validation errors.
     *
     * @param ConstraintViolationListInterface $validate
     * @return JsonResponse|null
     */
    public function returnValidationErrorsResponse(ConstraintViolationListInterface $validate): ?JsonResponse;
}