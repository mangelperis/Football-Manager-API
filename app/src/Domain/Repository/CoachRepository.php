<?php
declare(strict_types=1);


namespace App\Domain\Repository;

use App\Domain\Entity\Coach;
use App\Domain\Repository\Common\SaveInterface;
use App\Domain\Repository\Common\DeleteInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\Entity;
use Exception;

class CoachRepository extends EntityRepository implements SaveInterface, DeleteInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Coach::class));
        $this->entityManager = $entityManager;
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function save(object $entity): bool
    {
        try {
            /** @var Coach $entity */
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
            /** @var Coach $entity */
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}