<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'players')]
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
#[ORM\Index(columns: ['name'], name: 'name_index')]
class Player extends Employee implements ClubMember
{
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
}
