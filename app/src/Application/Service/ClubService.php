<?php
declare(strict_types=1);


namespace App\Application\Service;

use App\Application\DTO\ClubDetailsDTO;
use App\Application\DTO\ClubDTO;
use App\Domain\Entity\Club;
use App\Infrastructure\Adapter\DoctrineRepository\ClubRepositoryDoctrineAdapter;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

class ClubService
{
    public function __construct(
        private LoggerInterface               $logger,
        private ClubRepositoryDoctrineAdapter $clubRepository,
        private CoachService                  $coachService,
        private PlayerService                 $playerService,
    )
    {
    }


    /**
     * @param array $data
     * @return Club|null
     * @throws Exception
     */
    public function createClub(array $data): ?Club
    {
        try {
            $club = $this->clubRepository->findOneBy(['name' => $data['name']]);

            //Create one if not present in database
            if (!$club) {
                $club = new Club();
                $club->setName($data['name']);
            }

            //Update the Rest if existed before
            $club->setShortname(strtoupper($data['shortname']));
            $club->setCountry(strtoupper($data['country']));
            $club->setBudget($data['budget']);
            $club->setEmail($data['email']);

            $result = $this->clubRepository->save($club);

            if ($result) {
                return $club;
            }

            return null;
        } catch (Exception $exception) {
            // Handle Save Errors
            $this->logger->error(sprintf("[SERVICE] save fail: %s", $exception->getMessage()));
            throw new Exception('Error while saving club');
        }
    }


    /**
     * @param int $clubId
     * @param float $newBudget
     * @return void
     * @throws Exception
     */
    public function updateClubBudget(int $clubId, float $newBudget): void
    {
        try {
            $club = $this->clubRepository->find($clubId);

            if (!$club) {
                throw new \InvalidArgumentException('Club not found.', Response::HTTP_NOT_FOUND);
            }

            $currentBudget = $club->getBudget();

            if ($currentBudget === $newBudget) {
                throw new \InvalidArgumentException('New Budget cannot be same as Current Budget.', Response::HTTP_BAD_REQUEST);
            }

            $totalSalaries = $this->calculateTotalSalaries($club);

            if ($newBudget < $totalSalaries) {
                throw new \LogicException('New budget cannot be less than the total salaries of the club.', Response::HTTP_CONFLICT);
            }

            $club->setBudget($newBudget);

            $this->clubRepository->save($club);
        } catch (\InvalidArgumentException|\LogicException  $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Club update budget fail: %s", $exception->getMessage()));
            throw new Exception('Error while updating Club budget');
        }
    }

    /**
     * @param Club $club
     * @return float
     */
    private
    function calculateTotalSalaries(Club $club): float
    {
        $totalSalaries = 0;

        foreach ($club->getPlayers() as $player) {
            $totalSalaries += $player->getSalary();
        }

        foreach ($club->getCoaches() as $coach) {
            $totalSalaries += $coach->getSalary();
        }

        return $totalSalaries;
    }


    /**
     * @param int $clubId
     * @return array
     * @throws Exception
     */
    public function getClubDetails(int $clubId): array
    {
        try {
            // Retrieve the club data and create a ClubDTO
            /** @var Club $club */
            $club = $this->clubRepository->find($clubId);
            if (!$club) {
                throw new \InvalidArgumentException('Club not found.', Response::HTTP_NOT_FOUND);
            }

            $clubDTO = new ClubDTO(
                $club->getId(),
                $club->getName(),
                $club->getShortname(),
                $club->getCountry(),
                $club->getBudget(),
                $club->getEmail(),
            );

            // Retrieve the players and coaches DTO
            $playersDTO = $this->playerService->getPlayersByClub($clubId);
            $coachesDTO = $this->coachService->getCoachesByClub($clubId);


            $clubDetailsDTO = new ClubDetailsDTO(
                $clubDTO,
                $coachesDTO,
                $playersDTO
            );

            //Match expected array from createDtoResponse
            return [$clubDetailsDTO];
        } catch (\InvalidArgumentException|\LogicException  $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Get Club Details fail: %s", $exception->getMessage()));
            throw new Exception('Error while fetching Club Details');
        }

    }

}