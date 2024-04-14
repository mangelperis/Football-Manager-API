<?php

namespace App\Tests\Unit\Infrastructure\Adapter;

use App\Infrastructure\Adapter\ResponseHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ResponseHandlerTest extends TestCase
{
    private Serializer $serializer;
    private ResponseHandler $responseHandler;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(Serializer::class);
        $this->responseHandler = new ResponseHandler($this->serializer);
    }

    public function testCreateResponse(): void
    {
        $message = 'Test message';
        $statusCode = Response::HTTP_OK;

        $response = $this->responseHandler->createResponse($message, $statusCode);

        //Class, code, header & content msg
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(['message' => $message], json_decode($response->getContent(), true));
    }

    public function testCreateSuccessResponse(): void
    {
        $data = ['id' => 1, 'name' => 'Test'];
        $type = 'player';
        $statusCode = Response::HTTP_CREATED;

        $response = $this->responseHandler->createSuccessResponse($data, $type, $statusCode);

        //Class, code, header, header-location & content msg
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals("/api/{$type}/{$data['id']}", $response->headers->get('Location'));
        //Does ucfirst() on $type
        $this->assertEquals([
            'message' => 'Player created successfully.',
            'data' => $data,
        ], json_decode($response->getContent(), true));
    }

    public function testReturnErrorResponse(): void
    {
        $message = 'Error message';
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $errors = ['field' => 'Error description'];

        $response = $this->responseHandler->returnErrorResponse($message, $statusCode, $errors);

        //Class, code, header & content msg
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($statusCode, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals([
            'message' => $message,
            'errors' => $errors,
        ], json_decode($response->getContent(), true));
    }

    public function testCreateDtoResponse(): void
    {
        $dto = new \stdClass();
        $dto->id = 1;
        $dto->name = 'Test';

        //Normalize expects a mixed object
        $this->serializer->expects($this->once())
            ->method('normalize')
            ->with($dto)
            ->willReturn(['id' => 1, 'name' => 'Test']);

        $response = $this->responseHandler->createDtoResponse($dto);

        //Class, code, header & content msg
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals(['id' => 1, 'name' => 'Test'], json_decode($response->getContent(), true));
    }

    public function testReturnValidationErrorsResponseWithNoViolations(): void
    {
        //No errors
        $violations = new ConstraintViolationList();

        $response = $this->responseHandler->returnValidationErrorsResponse($violations);

        $this->assertNull($response);
    }
}
