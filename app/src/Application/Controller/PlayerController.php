<?php
declare(strict_types=1);

namespace App\Application\Controller;


use App\Application\Service\Handler\ResponseHandler;
use App\Application\Service\PlayerService;
use App\Domain\Entity\Player;
use App\Infrastructure\Validation\JsonSchemaValidator;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractFOSRestController
{
    const string TYPE = 'player';
    //Place it under Infrastructure\Validation\Schemas.
    const string PLAYER_JSON_SCHEMA = 'player.json';

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
     * @throws Exception
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


}