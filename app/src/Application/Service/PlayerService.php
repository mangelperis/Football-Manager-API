<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\DTO\PlayerDTO;
use App\Domain\Entity\Club;
use App\Domain\Entity\Player;
use App\Domain\Repository\NotifierInterface;
use App\Infrastructure\Persistence\Doctrine\ClubRepositoryDoctrineAdapter;
use App\Infrastructure\Persistence\Doctrine\PlayerRepositoryDoctrineAdapter;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class PlayerService
{

    public function __construct(
        private LoggerInterface                 $logger,
        private ValidatorInterface              $validator,
        private PlayerRepositoryDoctrineAdapter $playerRepository,
        private ClubRepositoryDoctrineAdapter   $clubRepository,
        private NotifierInterface               $notifier,
    )
    {
    }

    /**
     * @param array $data
     * @return Player|null
     * @throws Exception
     */
    public function createPlayer(array $data): ?Player
    {
        try {
            $player = $this->playerRepository->findOneBy(['email' => $data['email']]);

            //Create one if not present in database
            if (!$player) {
                $player = new Player();
                $player->setEmail($data['email']);
            }

            //Update Name & Position if existed before
            $player->setName($data['name']);
            $player->setPosition($data['position']);

            if (!$this->validator->validate($player)) {
                throw new \LogicException('Player is not valid.', Response::HTTP_BAD_REQUEST);
            }

            $result = $this->playerRepository->save($player);

            if ($result) {
                return $player;
            }

            return null;
        } catch (Exception $exception) {
            // Handle Save Errors
            $this->logger->error(sprintf("[SERVICE] save fail: %s", $exception->getMessage()));
            throw new Exception('Error while saving player');
        }

    }

    /**
     * @param int $playerId
     * @return void
     * @throws Exception
     */
    public function deletePlayer(int $playerId): void
    {
        try {
            /** @var Player $player */
            $player = $this->playerRepository->find($playerId);


            if (!$player) {
                throw new \InvalidArgumentException('Player not found.', Response::HTTP_NOT_FOUND);
            }

            $this->playerRepository->delete($player);
        } catch (\InvalidArgumentException $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode());
        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] delete fail: %s", $exception->getMessage()));
            throw new Exception('Error while deleting player');
        }

    }

    /**
     * @param int $playerId
     * @param int $clubId
     * @param float $salary
     * @return void
     * @throws Exception
     */
    public function attachToClub(int $playerId, int $clubId, float $salary): void
    {
        try {
            /** @var Player $player */
            $player = $this->playerRepository->find($playerId);
            /** @var Club $club */
            $club = $this->clubRepository->find($clubId);

            if (!$player || !$club) {
                throw new \InvalidArgumentException('Player or club not found.', Response::HTTP_NOT_FOUND);
            }

            if ($player->getClub()) {
                throw new \LogicException('Player is already attached to a club.', Response::HTTP_CONFLICT);
            }

            //Attach logic
            $club->addPlayer($player);
            $player->setClub($club);
            $player->setSalary($salary);

            $newBalance = $club->getBudget() - $salary;

            if ($newBalance < 0) {
                throw new \LogicException("Club balance is not enough to sign: {$player->getName()}.", Response::HTTP_CONFLICT);
            }

            $club->setBudget($newBalance);

            $this->playerRepository->save($player);
            $this->clubRepository->save($club);

            $this->notifier->notify(
                $player->getEmail(),
                $club->getEmail(),
                "Transfer to [{$club->getName()}] completed",
                "You have signed for {$club->toString()} with a salary of {$player->getSalary()} tokens."
            );

            $this->logger->log(0, sprintf("[NOTIFY] Mail Sent to [%s]", $player->getEmail()));

        } catch (\InvalidArgumentException|\LogicException  $exception) {
            throw new \LogicException($exception->getMessage(), $exception->getCode());
        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Player attach fail: %s", $exception->getMessage()));
            throw new Exception('Error while attaching player to a Club');
        }
    }


    /**
     * @param int $playerId
     * @return void
     * @throws Exception
     */
    public function removeFromClub(int $playerId): void
    {
        try {
            /** @var Player $player */
            $player = $this->playerRepository->find($playerId);

            if (!$player) {
                throw new \InvalidArgumentException('Player not found.', Response::HTTP_NOT_FOUND);
            }

            /** @var Club $club */
            $club = $player->getClub();

            if (!$club) {
                throw new \LogicException('Player is not attached to any club.', Response::HTTP_CONFLICT);
            }

            $club->removePlayer($player);
            $player->setClub(null);

            //Salary & Budget changes
            $club->setBudget($club->getBudget() + $player->getSalary());
            $player->setSalary(0);

            $this->playerRepository->save($player);
            $this->clubRepository->save($club);

            $this->notifier->notify(
                $player->getEmail(),
                $club->getEmail(),
                "Player removed from [{$club->getName()}]",
                'You have been removed from your club and now you\'re a free agent.'
            );
            $this->logger->log(0, sprintf("[NOTIFY] Mail Sent to [%s]", $player->getEmail()));

        } catch (\InvalidArgumentException|\LogicException  $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Player attach fail: %s", $exception->getMessage()));
            throw new Exception('Error while removing player from Club');
        }
    }

    /**
     * @param int $clubId
     * @param int $page
     * @param int $limit
     * @param string $filterName
     * @return array
     * @throws Exception
     */
    public function getPlayersByClub(int $clubId, int $page = 1, int $limit = 10, string $filterName = ''): array
    {
        try {
            $club = $this->clubRepository->find($clubId);

            if(!$club){
                throw new \InvalidArgumentException('Club not found.', Response::HTTP_NOT_FOUND);
            }

            $players = $this->playerRepository->findByClub($clubId, $page, $limit, $filterName);

            if(!$players){
                return [];
            }

            return array_map(function ($player) {
                return new PlayerDTO(
                    $player->getId(),
                    $player->getName(),
                    $player->getPosition(),
                    $player->getSalary(),
                    $player->getEmail(),
                );
            }, $players);

        } catch (\InvalidArgumentException|\LogicException  $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Get Club Players fail: %s", $exception->getMessage()));
            throw new Exception('Error while fetching Club Players');
        }
    }

}
