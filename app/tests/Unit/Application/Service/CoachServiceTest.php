<?php
declare(strict_types=1);

namespace App\Tests\Unit\Application\Service;

use App\Application\DTO\CoachDTO;
use App\Application\Service\CoachService;
use App\Domain\Entity\Club;
use App\Domain\Entity\Coach;
use App\Domain\Repository\NotifierInterface;
use App\Infrastructure\Adapter\DoctrineRepository\ClubRepositoryDoctrineAdapter;
use App\Infrastructure\Adapter\DoctrineRepository\CoachRepositoryDoctrineAdapter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CoachServiceTest extends TestCase
{
    private CoachService $coachService;
    private LoggerInterface $loggerMock;
    private ValidatorInterface $validatorMock;
    private CoachRepositoryDoctrineAdapter $coachRepositoryMock;
    private ClubRepositoryDoctrineAdapter $clubRepositoryMock;
    private NotifierInterface $notifierMock;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->coachRepositoryMock = $this->createMock(CoachRepositoryDoctrineAdapter::class);
        $this->clubRepositoryMock = $this->createMock(ClubRepositoryDoctrineAdapter::class);
        $this->notifierMock = $this->createMock(NotifierInterface::class);

        $this->coachService = new CoachService(
            $this->loggerMock,
            $this->validatorMock,
            $this->coachRepositoryMock,
            $this->clubRepositoryMock,
            $this->notifierMock
        );
    }

    /**
     * @throws \Exception
     */
    public function testCreateCoach(): ?Coach
    {
        $data = [
            'name' => 'Carlo',
            'email' => 'carlo@rm.com',
            'role' => 'Head',
        ];

        $coach = new Coach();
        $coach->setName($data['name']);
        $coach->setEmail($data['email']);
        $coach->setRole($data['role']);

        $this->coachRepositoryMock->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $data['email']]);

        $this->validatorMock->expects($this->once())
            ->method('validate')
            ->with($coach);

        $this->coachRepositoryMock->expects($this->atLeastOnce())
            ->method('save')
            ->with($coach)
            ->willReturn(true);

        $result = $this->coachService->createCoach($data);

        $this->assertInstanceOf(Coach::class, $result);
        $this->assertEquals($data['name'], $result->getName());
        $this->assertEquals($data['email'], $result->getEmail());
        $this->assertEquals($data['role'], $result->getRole());

        return $result;
    }

    /**
     * @throws \Exception
     */
    public function testDeleteCoach(): void
    {
        $coachId = 1;

        $coach = $this->testCreateCoach();

        $this->coachRepositoryMock->expects($this->once())
            ->method('find')
            ->with($coachId)
            ->willReturn($coach);

        $this->coachRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($coach);

        $this->coachService->deleteCoach($coachId);
    }

    /**
     * @throws \Exception
     */
    public function testAttachToClub(): void
    {
        $coachId = 1;
        $clubId = 1;
        $salary = 5000.0;

        $coach = new Coach();
        $coach->setName('Carlo');
        $coach->setRole('Head');
        $coach->setEmail('carlo@rm.com');

        $club = new Club();
        $club->setName('Liverpool');
        $club->setShortname('lfc');
        $club->setCountry('EN');
        $club->setEmail('info@lfc.com');
        $club->setBudget(10000.0);

        $this->coachRepositoryMock->expects($this->once())
            ->method('find')
            ->with($coachId)
            ->willReturn($coach);

        $this->clubRepositoryMock->expects($this->once())
            ->method('find')
            ->with($clubId)
            ->willReturn($club);

        $this->coachRepositoryMock->expects($this->once())
            ->method('save')
            ->with($coach);

        $this->clubRepositoryMock->expects($this->once())
            ->method('save')
            ->with($club);

        $this->notifierMock->expects($this->once())
            ->method('notify');

        $this->coachService->attachToClub($coachId, $clubId, $salary);

        $this->assertEquals($club, $coach->getClub());
        $this->assertEquals($salary, $coach->getSalary());
        //10000 - 5000
        $this->assertEquals(5000.0, $club->getBudget());
    }

    /**
     * @throws \Exception
     */
    public function testRemoveFromClub(): void
    {
        $coachId = 1;

        $coach = new Coach();
        $coach->setName('Carlo');
        $coach->setRole('Head');
        $coach->setEmail('carlo@rm.com');

        $club = new Club();
        $club->setName('Liverpool');
        $club->setShortname('lfc');
        $club->setCountry('EN');
        $club->setEmail('info@lfc.com');
        $club->setBudget(10000.0);

        $club->addCoach($coach);
        $coach->setClub($club);

        $this->coachRepositoryMock->expects($this->once())
            ->method('find')
            ->with($coachId)
            ->willReturn($coach);

        $this->coachRepositoryMock->expects($this->once())
            ->method('save')
            ->with($coach);

        $this->clubRepositoryMock->expects($this->once())
            ->method('save')
            ->with($club);

        $this->notifierMock->expects($this->once())
            ->method('notify');


        $this->coachService->removeFromClub($coachId);

        $this->assertNull($coach->getClub());
        $this->assertEquals(0.0, $coach->getSalary());
        $this->assertEquals(10000.0, $club->getBudget());
    }

    /**
     * @throws \Exception
     */
    public function testGetCoachesByClub(): void
    {
        $clubId = 1;
        $page = 1;
        $limit = 10;
        $filterName = 'Carlo';


        $club = new Club();
        $club->setName('Liverpool');
        $club->setShortname('lfc');
        $club->setCountry('EN');
        $club->setEmail('info@lfc.com');
        $club->setBudget(10000.0);

        $coach1 = new Coach();
        $coach1->setId(1);
        $coach1->setName('Carlo');
        $coach1->setRole('Head');
        $coach1->setEmail('carlo@rm.com');

        $coach2 = new Coach();
        $coach1->setId(2);
        $coach2->setName('Mourinho');
        $coach2->setRole('Head');
        $coach2->setEmail('mourinho@specialone.com');

        $this->clubRepositoryMock->expects($this->once())
            ->method('find')
            ->with($clubId)
            ->willReturn($club);

        $this->coachRepositoryMock->expects($this->once())
            ->method('findByClub')
            ->with($clubId, $page, $limit, $filterName)
            ->willReturn([$coach1]);

        $result = $this->coachService->getCoachesByClub($clubId, $page, $limit, $filterName);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertContainsOnlyInstancesOf(CoachDTO::class, $result);
    }
}
