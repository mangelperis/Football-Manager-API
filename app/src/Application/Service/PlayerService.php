<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Entity\Club;
use App\Domain\Entity\Player;
use App\Domain\Repository\NotifierInterface;
use App\Infrastructure\Persistence\Doctrine\ClubRepositoryDoctrineAdapter;
use App\Infrastructure\Persistence\Doctrine\PlayerRepositoryDoctrineAdapter;
use Exception;
use Psr\Log\LoggerInterface;
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
            if(!$player){
                $player = new Player();
                $player->setEmail($data['email']);
            }

            //Update Name & Position if existed before
            $player->setName($data['name']);
            $player->setPosition($data['position']);

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
                throw new \InvalidArgumentException('Player not found.');
            }

            $this->playerRepository->delete($player);
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
     */
    public function attachToClub(int $playerId, int $clubId, float $salary): void
    {
        try {
            /** @var Player $player */
            $player = $this->playerRepository->find($playerId);
            /** @var Club $club */
            $club = $this->clubRepository->find($clubId);

            if (!$player || !$club) {
                throw new \InvalidArgumentException('Player or club not found.');
            }

            if ($player->getClub()) {
                throw new \LogicException('Player is already attached to a club.');
            }

            //Attach logic
            $club->addPlayer($player);
            $player->setClub($club);
            $player->setSalary($salary);

            $newBalance = $club->getBudget() - $salary;

            if ($newBalance < 0) {
                throw new \LogicException("Club balance is not enough to sign: {$player->getName()}.");
            }

            $club->setBudget($newBalance);

            //Validation of entities before any change
            if (!$this->validator->validate($player) || !$this->validator->validate($club)) {
                $this->logger->notice(sprintf("[SERVICE] Invalid Player [%s]-[%s]", $player->getName(), $player->getClub()->toString()));
                throw new \LogicException('Invalid Player data.');
            }

            $this->playerRepository->save($player);
            $this->clubRepository->save($club);

            $this->notifier->notify(
                $player->getEmail(),
                "Transfer to [{$club->getName()}] completed",
                "You have signed for {$club->toString()} with a salary of {$player->getSalary()} tokens."
            );

            $this->logger->log(0, sprintf("[NOTIFY] Mail Sent to [%s]", $player->getEmail()));

        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Player attach fail: %s", $exception->getMessage()));
        }
    }


    /**
     * @param int $playerId
     * @return void
     */
    public function removeFromClub(int $playerId): void
    {
        try {
            /** @var Player $player */
            $player = $this->playerRepository->find($playerId);

            if (!$player) {
                throw new \InvalidArgumentException('Player not found.');
            }

            /** @var Club $club */
            $club = $player->getClub();

            if (!$club) {
                throw new \LogicException('Player is not attached to any club.');
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
                'Player removed from club',
                'You have been removed from your club and now you\'re a free agent.'
            );
            $this->logger->log(0, sprintf("[NOTIFY] Mail Sent to [%s]", $player->getEmail()));

        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Player attach fail: %s", $exception->getMessage()));

        }
    }

    /**
     * @param int $clubId
     * @param int $page
     * @param int $limit
     * @param string $filter
     * @return array
     */
    public function getPlayersByClub(int $clubId, int $page, int $limit, string $filter): array
    {
        return [];
        //   return $this->playerRepository->findByClub($clubId, $page, $limit, $filter);
    }

}
