<?php

declare(strict_types=1);

namespace App\Tests\Unit\Infrastructure\Adapter\Repository;

use App\Domain\Entity\Player;
use App\Instrastructure\Persistence\Doctrine\PlayerRepositoryDoctrineAdapter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class PlayerRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private PlayerRepositoryDoctrineAdapter $playerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with(Player::class)
            ->willReturn($classMetadata);

        $this->playerRepository = new PlayerRepositoryDoctrineAdapter($this->entityManager);
    }

    public function testSave(): void
    {
        $player = new Player();
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($player);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->playerRepository->save($player);

        $this->assertTrue($result);
    }

    public function testDelete(): void
    {
        $player = new Player();
        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($player);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $result = $this->playerRepository->delete($player);

        $this->assertTrue($result);
    }
}
