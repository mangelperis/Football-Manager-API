<?php
declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\Service\ClubService;
use App\Application\Service\CoachService;
use App\Application\Service\PlayerService;
use App\Domain\Entity\Club;
use App\Infrastructure\Adapter\DoctrineRepository\ClubRepositoryDoctrineAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ClubServiceTest extends TestCase
{
    private ClubService $clubService;
    private ClubRepositoryDoctrineAdapter $clubRepositoryMock;
    private LoggerInterface $loggerMock;

    protected function setUp(): void
    {
        $this->clubRepositoryMock = $this->createMock(ClubRepositoryDoctrineAdapter::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $coachServiceMock = $this->createMock(CoachService::class);
        $playerServiceMock = $this->createMock(PlayerService::class);

        $this->clubService = new ClubService(
            $this->loggerMock,
            $this->clubRepositoryMock,
            $coachServiceMock,
            $playerServiceMock
        );
    }

    /**
     * @throws \Exception
     */
    public function testCreateClub(): void
    {
        $data = [
            'name' => 'Castellon',
            'shortname' => 'C',
            'country' => 'es',
            'budget' => 1000,
            'email' => 'test@castellon.com',
        ];

        $club = new Club();
        $club->setName($data['name']);
        $club->setShortname(strtoupper($data['shortname']));
        $club->setCountry(strtoupper($data['country']));
        $club->setBudget($data['budget']);
        $club->setEmail($data['email']);

        $this->clubRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['name' => $data['name']])
            ->willReturn(null);

        $this->clubRepositoryMock->expects($this->once())
            ->method('save')
            ->with($club)
            ->willReturn(true);

        $result = $this->clubService->createClub($data);

        $this->assertInstanceOf(Club::class, $result);
        $this->assertEquals($data['name'], $result->getName());
        $this->assertEquals(strtoupper($data['shortname']), $result->getShortname());
        $this->assertEquals(strtoupper($data['country']), $result->getCountry());
        $this->assertEquals($data['budget'], $result->getBudget());
        $this->assertEquals($data['email'], $result->getEmail());
    }

    /**
     * @throws \Exception
     */
    public function testUpdateClubBudget(): void
    {
        $clubId = 1;
        $newBudget = 1500000;

        $club = new Club();
        $club->setBudget(1000000);

        $this->clubRepositoryMock->expects($this->once())
            ->method('find')
            ->with($clubId)
            ->willReturn($club);

        $this->clubRepositoryMock->expects($this->once())
            ->method('save')
            ->with($club);

        $this->clubService->updateClubBudget($clubId, $newBudget);

        $this->assertEquals($newBudget, $club->getBudget());
    }

}
