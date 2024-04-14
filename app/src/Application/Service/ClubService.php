<?php
declare(strict_types=1);


namespace App\Application\Service;

use App\Domain\Entity\Club;
use App\Domain\Repository\NotifierInterface;
use App\Infrastructure\Persistence\Doctrine\ClubRepositoryDoctrineAdapter;
use App\Infrastructure\Persistence\Doctrine\PlayerRepositoryDoctrineAdapter;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClubService
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
}