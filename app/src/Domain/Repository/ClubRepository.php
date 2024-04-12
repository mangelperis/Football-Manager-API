<?php
declare(strict_types=1);


namespace App\Domain\Repository;

use App\Domain\Entity\Club;
use App\Domain\Repository\Common\RepositoryOperationsInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;

class ClubRepository extends EntityRepository implements RepositoryOperationsInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Club::class));
        $this->entityManager = $entityManager;
    }


    /**
     * @param object $entity
     * @return bool
     */
    public function save(object $entity): bool
    {
        try {
            /** @var Club $entity */
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
            /** @var Club $entity */
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @param object $entity
     * @return object|null
     */
    public function update(string $attribute, mixed $value, object $entity): ?object
    {
        try {
            if (!$entity instanceof Club) {
                throw new \InvalidArgumentException(sprintf(
                    'The entity must be an instance of %s, %s given.',
                    Club::class,
                    get_class($entity)
                ));
            }

            $setter = 'set' . ucfirst($attribute);

            if (!method_exists($entity, $setter)) {
                throw new \InvalidArgumentException(sprintf(
                    'The setter method "%s" does not exist in the %s entity.',
                    $setter,
                    Club::class
                ));
            }

            $entity->$setter($value);

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush();

            return $entity;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param float $value
     * @param Club $club
     * @return object
     */
    public function updateBudget(float $value, Club $club): object
    {
        return $this->update('budget', $value, $club);
    }
}