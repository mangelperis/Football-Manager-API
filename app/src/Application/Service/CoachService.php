<?php
declare(strict_types=1);


namespace App\Application\Service;

use App\Application\DTO\CoachDTO;
use App\Domain\Entity\Club;
use App\Domain\Entity\Coach;
use App\Domain\Repository\NotifierInterface;
use App\Infrastructure\Persistence\Doctrine\ClubRepositoryDoctrineAdapter;
use App\Infrastructure\Persistence\Doctrine\CoachRepositoryDoctrineAdapter;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CoachService
{

    public function __construct(
        private LoggerInterface                 $logger,
        private ValidatorInterface              $validator,
        private CoachRepositoryDoctrineAdapter $coachRepository,
        private ClubRepositoryDoctrineAdapter   $clubRepository,
        private NotifierInterface               $notifier,
    )
    {
    }


    /**
     * @param array $data
     * @return Coach|null
     * @throws Exception
     */
    public function createCoach(array $data): ?Coach
    {
        try {
            $coach = $this->coachRepository->findOneBy(['email' => $data['email']]);

            //Create one if not present in database
            if (!$coach) {
                $coach = new Coach();
                $coach->setEmail($data['email']);
            }

            //Update Name & Role if existed before
            $coach->setName($data['name']);
            $coach->setRole($data['role']);

            if (!$this->validator->validate($coach)) {
                throw new \LogicException('Coach is not valid.', Response::HTTP_BAD_REQUEST);
            }

            $result = $this->coachRepository->save($coach);

            if ($result) {
                return $coach;
            }

            return null;
        } catch (Exception $exception) {
            // Handle Save Errors
            $this->logger->error(sprintf("[SERVICE] save fail: %s", $exception->getMessage()));
            throw new Exception('Error while saving coach');
        }

    }


    /**
     * @param int $coachId
     * @return void
     * @throws Exception
     */
    public function deleteCoach(int $coachId): void
    {
        try {
            /** @var Coach $coach */
            $coach = $this->coachRepository->find($coachId);


            if (!$coach) {
                throw new \InvalidArgumentException('Coach not found.', Response::HTTP_NOT_FOUND);
            }

            $this->coachRepository->delete($coach);
        } catch (\InvalidArgumentException $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode());
        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] delete fail: %s", $exception->getMessage()));
            throw new Exception('Error while deleting coach');
        }

    }

    /**
     * @param int $coachId
     * @param int $clubId
     * @param float $salary
     * @return void
     * @throws Exception
     */
    public function attachToClub(int $coachId, int $clubId, float $salary): void
    {
        try {
            /** @var Coach $coach */
            $coach = $this->coachRepository->find($coachId);
            /** @var Club $club */
            $club = $this->clubRepository->find($clubId);

            if (!$coach || !$club) {
                throw new \InvalidArgumentException('Coach or club not found.', Response::HTTP_NOT_FOUND);
            }

            if ($coach->getClub()) {
                throw new \LogicException('Coach is already attached to a club.', Response::HTTP_CONFLICT);
            }

            //Attach logic
            $club->addCoach($coach);
            $coach->setClub($club);
            $coach->setSalary($salary);

            $newBalance = $club->getBudget() - $salary;

            if ($newBalance < 0) {
                throw new \LogicException("Club balance is not enough to sign: {$coach->getName()}.", Response::HTTP_CONFLICT);
            }

            $club->setBudget($newBalance);

            $this->coachRepository->save($coach);
            $this->clubRepository->save($club);

            $this->notifier->notify(
                $coach->getEmail(),
                $club->getEmail(),
                "Transfer to [{$club->getName()}] completed",
                "You have signed for {$club->toString()} with a salary of {$coach->getSalary()} tokens."
            );

            $this->logger->log(0, sprintf("[NOTIFY] Mail Sent to [%s]", $coach->getEmail()));

        } catch (\InvalidArgumentException|\LogicException  $exception) {
            throw new \LogicException($exception->getMessage(), $exception->getCode());
        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Coach attach fail: %s", $exception->getMessage()));
            throw new Exception('Error while attaching coach to a Club');
        }
    }

    /**
     * @param int $coachId
     * @return void
     * @throws Exception
     */
    public function removeFromClub(int $coachId): void
    {
        try {
            /** @var Coach $coach */
            $coach = $this->coachRepository->find($coachId);

            if (!$coach) {
                throw new \InvalidArgumentException('Coach not found.', Response::HTTP_NOT_FOUND);
            }

            /** @var Club $club */
            $club = $coach->getClub();

            if (!$club) {
                throw new \LogicException('Coach is not attached to any club.', Response::HTTP_CONFLICT);
            }

            $club->removeCoach($coach);
            $coach->setClub(null);

            //Salary & Budget changes
            $club->setBudget($club->getBudget() + $coach->getSalary());
            $coach->setSalary(0);

            $this->coachRepository->save($coach);
            $this->clubRepository->save($club);

            $this->notifier->notify(
                $coach->getEmail(),
                $club->getEmail(),
                "Fired from [{$club->getName()}]",
                'You have been fired from your club and now you\'re a free agent.'
            );
            $this->logger->log(0, sprintf("[NOTIFY] Mail Sent to [%s]", $coach->getEmail()));

        } catch (\InvalidArgumentException|\LogicException  $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Coach attach fail: %s", $exception->getMessage()));
            throw new Exception('Error while removing coach from Club');
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
    public function getCoachesByClub(int $clubId, int $page = 1, int $limit = 10, string $filterName = ''): array
    {
        try {
            $club = $this->clubRepository->find($clubId);

            if(!$club){
                throw new \InvalidArgumentException('Club not found.', Response::HTTP_NOT_FOUND);
            }

            $coaches = $this->coachRepository->findByClub($clubId, $page, $limit, $filterName);

            if(!$coaches){
                return [];
            }

            return array_map(function ($coach) {
                return new CoachDTO(
                    $coach->getId(),
                    $coach->getName(),
                    $coach->getRole(),
                    $coach->getSalary(),
                    $coach->getEmail(),
                );
            }, $coaches);

        } catch (\InvalidArgumentException|\LogicException  $exception) {
            throw new \InvalidArgumentException($exception->getMessage(), $exception->getCode());

        } catch (Exception $exception) {
            $this->logger->error(sprintf("[SERVICE] Get Club Coaches fail: %s", $exception->getMessage()));
            throw new Exception('Error while fetching Club Coaches');
        }
    }

}