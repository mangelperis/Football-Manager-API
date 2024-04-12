<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Adapter\Repository;

use App\Domain\Entity\Coach;
use App\Instrastructure\Persistence\Doctrine\CoachRepositoryDoctrineAdapter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class CoachRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private CoachRepositoryDoctrineAdapter $coachRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with(Coach::class)
            ->willReturn($classMetadata);

        $this->coachRepository = new CoachRepositoryDoctrineAdapter($this->entityManager);
    }

    public function testSave(): void
    {
        $coach = new Coach();
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($coach);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->coachRepository->save($coach);

        $this->assertTrue($result);
    }

    public function testDelete(): void
    {
        $coach = new Coach();
        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($coach);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->coachRepository->delete($coach);

        $this->assertTrue($result);
    }
}
