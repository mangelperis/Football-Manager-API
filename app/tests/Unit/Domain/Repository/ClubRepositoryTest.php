<?php

declare(strict_types=1);

namespace App\Tests\Domain\Repository;

use App\Domain\Entity\Club;
use App\Domain\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class ClubRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private ClubRepository $clubRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with(Club::class)
            ->willReturn($classMetadata);

        $this->clubRepository = new ClubRepository($this->entityManager);
    }

    public function testSave(): void
    {
        $club = new Club();
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($club);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->clubRepository->save($club);

        $this->assertTrue($result);
    }

    public function testDelete(): void
    {
        $club = new Club();
        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($club);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->clubRepository->delete($club);

        $this->assertTrue($result);
    }

    public function testUpdate(): void
    {
        $club = new Club();
        $attribute = 'name';
        $value = 'Real Club New';

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($club);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->clubRepository->update($attribute, $value, $club);
        $this->assertInstanceOf(Club::class, $result);
        $this->assertEquals($value, $result->getName());
    }

    public function testUpdateBudget(): void
    {
        $club = new Club();
        $value = 1.5;

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($club);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->clubRepository->updateBudget($value, $club);

        $this->assertInstanceOf(Club::class, $result);
        $this->assertEquals($value, $result->getBudget());
    }
}
