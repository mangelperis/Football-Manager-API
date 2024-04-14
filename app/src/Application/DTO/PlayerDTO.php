<?php
declare(strict_types=1);


namespace App\Application\DTO;

class PlayerDTO
{
    private int $id;
    private string $name;
    private string $position;
    private float $salary;
    private string $email;

    public function __construct(
        int $id,
        string $name,
        string $position,
        float $salary,
        string $email
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->position = $position;
        $this->salary = $salary;
        $this->email = $email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function getSalary(): float
    {
        return $this->salary;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'position' => $this->position,
            'salary' => $this->salary,
            'email' => $this->email,
        ];
    }

}