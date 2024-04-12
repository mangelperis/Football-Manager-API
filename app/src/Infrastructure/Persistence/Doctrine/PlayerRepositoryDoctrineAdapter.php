<?php
declare(strict_types=1);


namespace App\Infrastructure\Persistence\Doctrine;

use App\Domain\Entity\Player;
use App\Domain\Repository\Common\DeleteInterface;
use App\Domain\Repository\Common\SaveInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;

class PlayerRepositoryDoctrineAdapter extends EntityRepository implements SaveInterface, DeleteInterface
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Player::class));
        $this->entityManager = $entityManager;
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function save(object $entity): bool
    {
        try {
            /** @var Player $entity */
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function delete(object $entity): bool
    {
        try {
            /** @var Player $entity */
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}