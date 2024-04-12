<?php
declare(strict_types=1);

namespace App\Application\Controller;


use App\Application\Service\Handler\ResponseHandler;
use App\Application\Service\PlayerService;
use App\Domain\Entity\Player;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractFOSRestController
{
    const string TYPE = 'player';

    private PlayerService $playerService;
    private ResponseHandler $handler;
    private LoggerInterface $logger;

    public function __construct(
        PlayerService   $playerService,
        ResponseHandler $handler,
        LoggerInterface $logger
    )
    {
        $this->playerService = $playerService;
        $this->handler = $handler;
        $this->logger = $logger;
    }

    #[Route('/player', name: 'create_player', methods: ['POST'])]
    public function createPlayer(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate the request data
        // .TO DO json-schema validation

        try {
            /** @var Player $player */
            $player = $this->playerService->createPlayer($data);

            if ($player) {
                $this->logger->log(0, 'Created player', ['player' => $player]);
                return $this->handler->createSuccessResponse($player->toArray(), self::TYPE);
            }

            return $this->handler->createErrorResponse('Something went wrong', Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            $this->logger->error("[API] Create player error: {$e->getMessage()}");
            return $this->handler->createErrorResponse('Something went wrong');
        }
    }

}