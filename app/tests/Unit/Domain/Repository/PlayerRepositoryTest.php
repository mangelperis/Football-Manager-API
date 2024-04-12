<?php

declare(strict_types=1);

namespace App\Tests\Domain\Repository;

use App\Domain\Entity\Player;
use App\Domain\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;

class PlayerRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private PlayerRepository $playerRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with(Player::class)
            ->willReturn($classMetadata);

        $this->playerRepository = new PlayerRepository($this->entityManager);
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
