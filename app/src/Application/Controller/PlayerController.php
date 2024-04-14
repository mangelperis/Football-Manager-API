<?php
declare(strict_types=1);

namespace App\Application\Controller;


use App\Application\Service\PlayerService;
use App\Domain\Entity\Player;
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

class PlayerController extends AbstractFOSRestController
{
    const string TYPE = 'player';
    //Place it under Infrastructure\Validation\Schemas.
    const string PLAYER_JSON_SCHEMA = 'player.json';
    const int DEFAULT_PER_PAGE = 10;

    private PlayerService $playerService;
    private JsonSchemaValidator $jsonValidator;
    private ResponseHandler $responseHandler;
    private LoggerInterface $logger;

    public function __construct(
        PlayerService   $playerService,
        ResponseHandler $responseHandler,
        LoggerInterface $logger
    )
    {
        $this->playerService = $playerService;
        $this->jsonValidator = new JsonSchemaValidator(self::PLAYER_JSON_SCHEMA);
        $this->responseHandler = $responseHandler;
        $this->logger = $logger;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/player', name: 'create_player', methods: ['POST'])]
    public function createPlayer(Request $request): JsonResponse
    {
        try {
            //Validate input data against schema
            if (!$this->jsonValidator->validate($request->getContent())) {
                return $this->responseHandler->returnErrorResponse('Source JSON is not valid', Response::HTTP_UNPROCESSABLE_ENTITY, $this->jsonValidator->getErrors());
            }

            //IsValid
            $data = $this->jsonValidator->getDataObject();
            /** @var Player $player */
            $player = $this->playerService->createPlayer($data);

            if ($player) {
                $this->logger->log(0, 'Created player', ['player' => $player]);
                return $this->responseHandler->createSuccessResponse($player->toArray(), self::TYPE);
            }

            return $this->responseHandler->returnErrorResponse('Something went wrong', Response::HTTP_BAD_REQUEST);
        } catch (\InvalidArgumentException|\LogicException $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $this->logger->error("[API] Create player error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong');
        }

    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/player/{id}', name: 'delete_player', methods: ['DELETE'])]
    public function deletePlayer(int $id): JsonResponse
    {
        try {
            $this->playerService->deletePlayer($id);

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

            //Return validation errors
            if (null !== $this->responseHandler->returnValidationErrorsResponse($validate)) {
                return $this->responseHandler->returnValidationErrorsResponse($validate);
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


    /**
     * @param Request $request
     * @param int $clubId
     * @return JsonResponse
     * @throws Exception
     */
    #[Route('/club/{clubId}/players', name: 'list_club_players', methods: ['GET'])]
    public function listClubPlayers(Request $request, int $clubId): JsonResponse
    {
        try {
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', self::DEFAULT_PER_PAGE);
            $filterName = $request->query->get('name', '');

            $playerListDTOs = $this->playerService->getPlayersByClub($clubId, $page, $limit, $filterName);

            return $this->responseHandler->createDtoResponse($playerListDTOs);

        } catch (\InvalidArgumentException|\LogicException $e) {
            return $this->responseHandler->returnErrorResponse($e->getMessage(), $e->getCode());
        } catch (Exception $e) {
            $this->logger->error("[API] Fetch Club Players error: {$e->getMessage()}");
            return $this->responseHandler->returnErrorResponse('Something went wrong');
        }
    }


}