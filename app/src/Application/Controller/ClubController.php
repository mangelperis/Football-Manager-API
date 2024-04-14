<?php
declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\Service\ClubService;
use App\Application\Service\Handler\ResponseHandler;
use App\Domain\Entity\Club;
use App\Infrastructure\Validation\JsonSchemaValidator;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ClubController extends AbstractFOSRestController
{
    //Place it under Infrastructure\Validation\Schemas.
    const string CLUB_JSON_SCHEMA = 'club.json';
    const string TYPE = 'club';
    private ClubService $clubService;
    private JsonSchemaValidator $jsonValidator;
    private ResponseHandler $responseHandler;
    private LoggerInterface $logger;


    public function __construct(
        ClubService     $clubService,
        ResponseHandler $responseHandler,
        LoggerInterface $logger
    )
    {
        $this->clubService = $clubService;
        $this->jsonValidator = new JsonSchemaValidator(self::CLUB_JSON_SCHEMA);
        $this->responseHandler = $responseHandler;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/club', name: 'create_club', methods: ['POST'])]
    public function createClub(Request $request): JsonResponse
    {
        try {
            //Validate input data against schema
            if (!$this->jsonValidator->validate($request->getContent())) {
                return $this->responseHandler->returnErrorResponse('Source JSON is not valid', Response::HTTP_UNPROCESSABLE_ENTITY, $this->jsonValidator->getErrors());
            }

            //IsValid
            $data = $this->jsonValidator->getDataObject();

            /** @var Club $club */
            $club = $this->clubService->createClub($data);

            if ($club) {
                $this->logger->log(0, 'Created club', ['club' => $club]);
                return $this->responseHandler->createSuccessResponse($club->toArray(), self::TYPE);
            }

            return $this->responseHandler->returnErrorResponse('Something went wrong', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error("[API] Create club error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong' . $e->getMessage());
        }
    }


    #[Route('/club/{clubId}/budget', name: 'update_club_budget', methods: ['PUT'])]
    public function updateClubBudget(Request $request, int $clubId, ValidatorInterface $validator): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            //Check that source data is valid
            $constraints = new Assert\Collection([
                'budget' => [
                    new Assert\NotBlank(),
                    new Assert\Type('float'),
                    new Assert\GreaterThanOrEqual(0),
                ],
            ]);

            $validate = $validator->validate($data, $constraints);

            //Return validation errors
            if (null !== $this->responseHandler->returnValidationErrorsResponse($validate)) {
                return $this->responseHandler->returnValidationErrorsResponse($validate);
            }

            $newBudget = (float)$data['budget'];

            $this->clubService->updateClubBudget($clubId, $newBudget);

            return $this->responseHandler->createResponse('Club budget updated successfully');

        } catch (\InvalidArgumentException|\LogicException $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $this->logger->error("[API] Update club budget error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong');
        }
    }
}