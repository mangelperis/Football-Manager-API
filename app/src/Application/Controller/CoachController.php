<?php
declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\Service\CoachService;
use App\Domain\Entity\Coach;
use App\Domain\Port\ResponseHandlerInterface;
use App\Infrastructure\Adapter\ResponseHandler;
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


class CoachController extends AbstractFOSRestController
{
    const string TYPE = 'coach';
    //Place it under Infrastructure\Validation\Schemas.
    const string COACH_JSON_SCHEMA = 'coach.json';
    const int DEFAULT_PER_PAGE = 10;


    private CoachService $coachService;
    private JsonSchemaValidator $jsonValidator;
    private ResponseHandlerInterface $responseHandler;
    private LoggerInterface $logger;

    public function __construct(
        CoachService    $coachService,
        ResponseHandlerInterface $responseHandler,
        LoggerInterface $logger
    )
    {
        $this->coachService = $coachService;
        $this->jsonValidator = new JsonSchemaValidator(self::COACH_JSON_SCHEMA);
        $this->responseHandler = $responseHandler;
        $this->logger = $logger;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/coach', name: 'create_coach', methods: ['POST'])]
    public function createCoach(Request $request): JsonResponse
    {
        try {
            //Validate input data against schema
            if (!$this->jsonValidator->validate($request->getContent())) {
                return $this->responseHandler->returnErrorResponse('Source JSON is not valid', Response::HTTP_UNPROCESSABLE_ENTITY, $this->jsonValidator->getErrors());
            }

            //IsValid
            $data = $this->jsonValidator->getDataObject();
            /** @var Coach $coach */
            $coach = $this->coachService->createCoach($data);

            if ($coach) {
                $this->logger->log(0, 'Created coach', ['coach' => $coach]);
                return $this->responseHandler->createSuccessResponse($coach->toArray(), self::TYPE);
            }

            return $this->responseHandler->returnErrorResponse('Something went wrong', Response::HTTP_BAD_REQUEST);
        } catch (\InvalidArgumentException|\LogicException $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $this->logger->error("[API] Create coach error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong');
        }

    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/coach/{id}', name: 'delete_coach', methods: ['DELETE'])]
    public function deleteCoach(int $id): JsonResponse
    {
        try {
            $this->coachService->deleteCoach($id);

            return $this->responseHandler->createResponse('', Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Request $request
     * @param int $clubId
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/club/{clubId}/coach', name: 'attach_coach_to_club', methods: ['POST'])]
    public function attachCoachToClub(Request $request, int $clubId, ValidatorInterface $validator): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException(sprintf('Error decoding JSON: %s', json_last_error_msg()), Response::HTTP_BAD_REQUEST);
            }

            //Check that source data is valid
            $constraints = new Assert\Collection([
                'coachId' => [
                    new Assert\NotBlank(),
                    new Assert\Type('integer'),
                ],
                'salary' => [
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

            $coachId = $data['coachId'];
            $salary = $data['salary'];

            $this->coachService->attachToClub($coachId, $clubId, $salary);

            return $this->responseHandler->createResponse('Coach attached to club successfully');
        } catch (\InvalidArgumentException|\LogicException $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $this->logger->error("[API] Attach coach to club error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong');
        }
    }

    /**
     * @param Request $request
     * @param int $coachId
     * @return JsonResponse
     */
    #[Route('/coach/{coachId}/club', name: 'remove_coach_from_club', methods: ['DELETE'])]
    public function removeCoachFromClub(Request $request, int $coachId): JsonResponse
    {
        try {
            $this->coachService->removeFromClub($coachId);

            return $this->responseHandler->createResponse('Coach removed from club successfully');
        } catch (\InvalidArgumentException|\LogicException $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $this->logger->error("[API] Remove coach from club error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong');
        }
    }


    /**
     * @param Request $request
     * @param int $clubId
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/club/{clubId}/coaches', name: 'list_club_coaches', methods: ['GET'])]
    public function listClubCoaches(Request $request, int $clubId): JsonResponse
    {
        try {
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', self::DEFAULT_PER_PAGE);
            $filterName = $request->query->get('name', '');

            $coachListDTOs = $this->coachService->getCoachesByClub($clubId, $page, $limit, $filterName);
            return $this->responseHandler->createDtoResponse($coachListDTOs);

        } catch (\InvalidArgumentException|\LogicException $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $this->logger->error("[API] Fetch Club Coaches error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong');
        }
    }

}