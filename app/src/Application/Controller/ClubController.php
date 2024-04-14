<?php
declare(strict_types=1);

namespace App\Application\Controller;

use App\Application\Service\ClubService;
use App\Application\Service\Handler\ResponseHandler;
use App\Application\Service\PlayerService;
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
    private PlayerService $playerService;
    private ClubService $clubService;
    private JsonSchemaValidator $jsonValidator;
    private ResponseHandler $responseHandler;
    private LoggerInterface $logger;


    public function __construct(
        ClubService     $clubService,
        PlayerService   $playerService,
        ResponseHandler $responseHandler,
        LoggerInterface $logger
    )
    {
        $this->playerService = $playerService;
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


    /**
     * @param Request $request
     * @param int $clubId
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    #[Route('/club/{clubId}/player', name: 'attach_player_to_club', methods: ['POST'])]
    public function attachPlayerToClub(Request $request, int $clubId, ValidatorInterface $validator): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException(sprintf('Error decoding JSON: %s', json_last_error_msg()), Response::HTTP_BAD_REQUEST);
            }

            //Check that source data is valid
            $constraints = new Assert\Collection([
                'playerId' => [
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

            //Return errors
            if (count($validate) > 0) {
                $errors = [];

                foreach ($validate as $error) {
                    $errors[$error->getPropertyPath()] = $error->getMessage();
                }
                return $this->responseHandler->returnErrorResponse('Invalid source data', Response::HTTP_BAD_REQUEST, $errors);
            }


            $playerId = $data['playerId'];
            $salary = $data['salary'];

            $this->playerService->attachToClub($playerId, $clubId, $salary);

            return $this->responseHandler->createResponse('Player attached to club successfully');
        } catch (\InvalidArgumentException|\LogicException $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $this->logger->error("[API] Attach player to club error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong');
        }
    }

    /**
     * @param Request $request
     * @param int $playerId
     * @return JsonResponse
     */
    #[Route('/player/{playerId}/club', name: 'remove_player_from_club', methods: ['DELETE'])]
    public function removePlayerFromClub(Request $request, int $playerId): JsonResponse
    {
        try {
            $this->playerService->removeFromClub($playerId);

            return $this->responseHandler->createResponse('Player removed from club successfully');
        } catch (\InvalidArgumentException|\LogicException $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $this->logger->error("[API] Remove player from club error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong');
        }
    }
}