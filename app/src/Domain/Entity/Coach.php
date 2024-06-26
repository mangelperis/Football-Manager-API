<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
#[ORM\Table(name: 'coaches')]
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
#[ORM\Index(columns: ['name'], name: 'name_index')]
class Coach extends Employee implements ClubMember
{
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[ORM\Column(type: 'string', length: 20)]
    private string $role;

    #[ORM\ManyToOne(targetEntity: Club::class, inversedBy: 'coaches')]
    #[ORM\JoinColumn(name: 'club_id', referencedColumnName: 'id')]
    private ?Club $club = null;

    public function getClub(): ?Club
    {
        return $this->club;
    }

    public function setClub(?Club $club): self
    {
        $this->club = $club;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'role' => $this->getRole(),
            'salary' => $this->getSalary(),
            'email' => $this->getEmail(),
            'created' => $this->getCreated()->format('Y-m-d H:i:s'),
        ];
    }
}
