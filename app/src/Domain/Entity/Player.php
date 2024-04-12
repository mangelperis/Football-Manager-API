<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'players')]
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
#[ORM\Index(columns: ['name'], name: 'name_index')]
class Player extends Employee implements ClubMember
{

    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[ORM\Column(type: 'string', length: 20)]
    private string $position;

    #[ORM\ManyToOne(targetEntity: Club::class, inversedBy: 'players')]
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

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
          'id' => $this->getId(),
          'name' => $this->getName(),
          'position' => $this->getPosition(),
          'salary' => $this->getSalary(),
          'email' => $this->getEmail(),
          'created' => $this->getCreated()->format('Y-m-d H:i:s'),
        ];
    }
}
