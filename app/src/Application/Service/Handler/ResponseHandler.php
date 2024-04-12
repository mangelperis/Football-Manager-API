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
            'data' => $data,
            'message' => sprintf("%s created successfully.", ucfirst($type)),

        ], $statusCode, $headers);

        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Location', '/api/pla');

        return $response;
    }


    /**
     * @param $message
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    public function createErrorResponse($message, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, array $headers = []): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'error' => $message,
        ], $statusCode, $headers);
    }
}