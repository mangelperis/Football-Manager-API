<?php
declare(strict_types=1);


namespace App\Application\DTO;

class ClubDetailsDTO
{
    public function __construct(
        private ClubDTO $club,
        private array   $coaches,
        private array   $players,
    )
    {
    }

    public function getClub(): ClubDTO
    {
        return $this->club;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function getCoaches(): array
    {
        return $this->coaches;
    }

    public function toArray(): array
    {
        return [
            $this->club,
            'coaches' => $this->coaches,
            'players' => $this->players,
        ];
    }
}