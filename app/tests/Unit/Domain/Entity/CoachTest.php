<?php


use App\Domain\Entity\Club;
use PHPUnit\Framework\TestCase;
use App\Domain\Entity\Coach;

class CoachTest extends TestCase
{

    public function testCoachCreation(): Coach
    {
        $coach = new Coach();
        $coach->setName('John Doe');
        $coach->setSalary(200000);
        $coach->setEmail('john@example.com');
        $coach->setRole('manager');

        $this->assertInstanceOf(Coach::class, $coach);
        $this->assertEquals('John Doe', $coach->getName());
        $this->assertEquals(200000, $coach->getSalary());
        $this->assertEquals('john@example.com', $coach->getEmail());
        $this->assertEquals('manager', $coach->getRole());

        return $coach;
    }

    public function testCoachClub(): void
    {
        $coach = $this->testCoachCreation();
        $club = new Club();

        $coach->setClub($club);
        $this->assertInstanceOf(Club::class, $coach->getClub());
        $this->assertEquals($club, $coach->getClub());
    }
}
