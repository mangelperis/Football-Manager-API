<?php
declare(strict_types=1);


namespace App\Infrastructure\Adapter\DoctrineRepository;

use App\Domain\Entity\Coach;
use App\Domain\Repository\Common\DeleteInterface;
use App\Domain\Repository\Common\SaveInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;

class CoachRepositoryDoctrineAdapter extends EntityRepository implements SaveInterface, DeleteInterface
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


    /**
     * @param int $clubId
     * @param int $page
     * @param int $limit
     * @param string $filter
     * @return array
     */
    public function findByClub(int $clubId, int $page, int $limit, string $filter): array
    {
        $queryBuilder = $this->createQueryBuilder('coach')
            ->where('coach.club = :clubId')
            ->setParameter('clubId', $clubId)
            ->orderBy('coach.role', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        if (!empty($filter)) {
            $queryBuilder->andWhere('coach.name LIKE :filter')
                ->setParameter('filter', '%' . $filter . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }
}