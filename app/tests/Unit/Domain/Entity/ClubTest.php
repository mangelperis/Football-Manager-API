<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Club;
use App\Domain\Entity\Player;
use App\Domain\Entity\Coach;
use PHPUnit\Framework\TestCase;

class ClubTest extends TestCase
{
    public function testClubCreation(): void
    {
        $club = new Club();
        $club->setName('Test Club');
        $club->setShortname('TCB');
        $club->setCountry('US');
        $club->setBudget(1000000.0);

        $this->assertInstanceOf(Club::class, $club);
        $this->assertEquals('Test Club', $club->getName());
        $this->assertEquals('TCB', $club->getShortname());
        $this->assertEquals('US', $club->getCountry());
        $this->assertEquals(1000000.0, $club->getBudget());
    }

    public function testAddRemovePlayer(): void
    {
        $club = new Club();
        $player = new Player();

        $club->addPlayer($player);
        $this->assertCount(1, $club->getPlayers());
        $this->assertTrue($club->getPlayers()->contains($player));

        $club->removePlayer($player);
        $this->assertCount(0, $club->getPlayers());
        $this->assertFalse($club->getPlayers()->contains($player));
    }

    public function testAddRemoveCoach(): void
    {
        $club = new Club();
        $coach = new Coach();

        $club->addCoach($coach);
        $this->assertCount(1, $club->getCoaches());
        $this->assertTrue($club->getCoaches()->contains($coach));

        $club->removeCoach($coach);
        $this->assertCount(0, $club->getCoaches());
        $this->assertFalse($club->getCoaches()->contains($coach));
    }

    public function testToString(): void
    {
        $club = new Club();
        $club->setName('Test Club');
        $club->setShortname('TCB');
        $club->setCountry('US');

        $this->assertEquals('TCB-Test Club-US', $club->toString());
    }
}
