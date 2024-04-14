<?php
declare(strict_types=1);


namespace App\Infrastructure\Adapter;

use App\Domain\Port\ResponseHandlerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ResponseHandler implements ResponseHandlerInterface
{
    public function __construct(
        private SerializerInterface $serializer,
    )
    {
    }

    /**
     * @param string $message
     * @param int $statusCode
     * @return JsonResponse
     */
    public function createResponse(string $message, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        $response = new JsonResponse(
            ['message' => $message],
            $statusCode, []);

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /** ONLY FOR POST / CREATE ELEMENTS includes Location Header
     * @param array $data
     * @param string $type
     * @param int $statusCode
     * @param array $headers
     * @return JsonResponse
     */
    public function createSuccessResponse(array $data, string $type, int $statusCode = Response::HTTP_CREATED, array $headers = []): JsonResponse
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
    public function returnErrorResponse(string $message, int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR, array $errors = null, array $headers = []): JsonResponse
    {
        $response = new JsonResponse([
            'message' => $message,
            'errors' => $errors,
        ], $statusCode, $headers);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param array $DTO
     * @return JsonResponse
     */
    public function createDtoResponse(mixed $DTO): JsonResponse
    {
        //DTO must have toArray method defined
        //This changes the DTO element order, sadly
        /** @var array $data */
        $data = $this->serializer->normalize($DTO);

        $response = new JsonResponse($data, Response::HTTP_OK);

        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @param ConstraintViolationListInterface $validate
     * @return JsonResponse|null
     */
    public function returnValidationErrorsResponse(ConstraintViolationListInterface $validate): ?JsonResponse
    {
        //Return errors
        if (count($validate) > 0) {
            $errors = [];

            foreach ($validate as $error) {
                $errors[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->returnErrorResponse('Invalid source data', Response::HTTP_BAD_REQUEST, $errors);
        }

        return null;
    }
}