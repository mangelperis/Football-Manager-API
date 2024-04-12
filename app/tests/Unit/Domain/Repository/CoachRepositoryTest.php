<?php

declare(strict_types=1);

namespace App\Tests\Domain\Repository;

use App\Domain\Entity\Coach;
use App\Domain\Repository\CoachRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class CoachRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private CoachRepository $coachRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with(Coach::class)
            ->willReturn($classMetadata);

        $this->coachRepository = new CoachRepository($this->entityManager);
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
