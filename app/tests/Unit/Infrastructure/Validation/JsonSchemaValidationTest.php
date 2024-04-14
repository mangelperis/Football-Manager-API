<?php

namespace App\Tests\Unit\Infrastructure\Validation;

use App\Application\Controller\ClubController;
use App\Application\Controller\CoachController;
use App\Application\Controller\PlayerController;
use App\Infrastructure\Validation\JsonSchemaValidator;
use PHPUnit\Framework\TestCase;

class JsonSchemaValidationTest extends TestCase
{
    private  JsonSchemaValidator $clubValidator;
    private  JsonSchemaValidator $playerValidator;
    private  JsonSchemaValidator $coachValidator;

    protected function setUp(): void
    {
        $this->clubValidator = new JsonSchemaValidator(ClubController::CLUB_JSON_SCHEMA);
        $this->playerValidator = new JsonSchemaValidator(PlayerController::PLAYER_JSON_SCHEMA);
        $this->coachValidator = new JsonSchemaValidator(CoachController::COACH_JSON_SCHEMA);
    }


    /**
     * @throws \Exception
     */
    public function testPlayerIsValid(): void
    {
        $data = json_encode([
            'name' => 'John Doe',
            'position' => 'F',
            'email' => 'john@example.com',
        ]);

        $this->assertTrue($this->playerValidator->validate($data));
    }

    /**
     * @throws \Exception
     */
    public function testPlayerNotValid(): void
    {
        //Bad position
        $data = json_encode([
            'name' => 'John Doe',
            'position' => 'DEFENDER',
            'email' => 'john@example.com',
        ]);

        $this->assertFalse($this->playerValidator->validate($data));
    }

    /**
     * @throws \Exception
     */
    public function testCoachIsValid(): void
    {
        $data = json_encode([
            'name' => 'John Doe',
            'role' => 'Head',
            'email' => 'john@example.com',
        ]);

        $this->assertTrue($this->coachValidator->validate($data));
    }

    /**
     * @throws \Exception
     */
    public function testCoachNotValid(): void
    {
        //Bad Role
        $data = json_encode([
            'name' => 'John Doe',
            'role' => 'Prime',
            'email' => 'john@example.com',
        ]);

        $this->assertFalse($this->coachValidator->validate($data));
    }

    /**
     * @throws \Exception
     */
    public function testClubIsValid(): void
    {
        $data = json_encode([
            'name' => 'Liverpool',
            'shortname' => 'LFC',
            'country' => 'EN',
            'budget' => 125.5,
            'email' => 'lfc@example.com',
        ]);

        $this->assertTrue($this->clubValidator->validate($data));
    }

    /**
     * @throws \Exception
     */
    public function testClubNotValid(): void
    {
        //invalid country (2-alpha), string budget, and email
        $data = json_encode([
            'name' => 'Liverpool',
            'shortname' => 'LFC',
            'country' => 'ENG',
            'budget' => "one-hundred",
            'email' => 'jajaja',
        ]);

        $this->assertFalse($this->clubValidator->validate($data));
        $this->assertCount(3, $this->clubValidator->getErrors());
    }

}
