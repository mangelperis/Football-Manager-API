<?php
declare(strict_types=1);


namespace App\Application\Service\Handler;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ResponseHandler
{
    /**
     * @param $data
     * @param int $statusCode
     * @return JsonResponse
     */
    public function createResponse($data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse($data, $statusCode, [], true);
    }

    /**
     * @param $data
     * @param string $type
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    public function createSuccessResponse($data, string $type, int $statusCode = Response::HTTP_CREATED, array $headers = []): JsonResponse
    {
        $response = new JsonResponse([
            'message' => sprintf("%s created successfully.", ucfirst($type)),
            'data' => $data,
        ], $statusCode, $headers);

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Location', "/api/{$type}/{$data['id']}");

        return $response;
    }


    /**
     * @param string $message
     * @param array|null $errors
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    public function createErrorResponse(string $message, array $errors = null, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, array $headers = []): JsonResponse
    {
        $response = new JsonResponse([
            'message' => $message,
            'errors' => $errors,
        ], $statusCode, $headers);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}