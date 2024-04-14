<?php
declare(strict_types=1);


namespace App\Application\DTO;

class ClubDTO
{
    private int $id;
    private string $name;
    private string $shortName;
    private string $country;
    private float $budget;
    private string $email;

    public function __construct(
        int    $id,
        string $name,
        string $shortName,
        string $country,
        float  $budget,
        string $email
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->shortName = $shortName;
        $this->country = $country;
        $this->budget = $budget;
        $this->email = $email;
    }


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'shortname' => $this->shortName,
            'country' => $this->country,
            'budget' => $this->budget,
            'email' => $this->email,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getBudget(): float
    {
        return $this->budget;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}