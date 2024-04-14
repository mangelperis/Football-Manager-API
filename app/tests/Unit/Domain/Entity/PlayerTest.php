<?php
declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Club;
use App\Domain\Entity\Player;
use PHPUnit\Framework\TestCase;

class PlayerTest extends TestCase
{

    public function testPlayerCreation(): Player
    {
        $player = new Player();
        $player->setName('John Doe');
        $player->setSalary(100000);
        $player->setEmail('john@example.com');
        $player->setPosition('defender');

        $this->assertInstanceOf(Player::class, $player);
        $this->assertEquals('John Doe', $player->getName());
        $this->assertEquals(100000, $player->getSalary());
        $this->assertEquals('john@example.com', $player->getEmail());
        $this->assertEquals('defender', $player->getPosition());

        return $player;
    }

    public function testPlayerClub(): void
    {
        $player = $this->testPlayerCreation();
        $club = new Club();

        $player->setClub($club);
        $this->assertInstanceOf(Club::class, $player->getClub());
        $this->assertEquals($club, $player->getClub());

        $player->setClub(null);
        $this->assertNull($player->getClub());
    }
}
