<?php
declare(strict_types=1);


namespace App\Application\DTO;

class CoachDTO
{
    private int $id;
    private string $name;
    private string $role;
    private float $salary;
    private string $email;

    public function __construct(
        int    $id,
        string $name,
        string $role,
        float  $salary,
        string $email
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->role = $role;
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

    public function getRole(): string
    {
        return $this->role;
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
            'role' => $this->role,
            'salary' => $this->salary,
            'email' => $this->email,
        ];
    }
}