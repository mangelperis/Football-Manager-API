<?php

namespace App\Domain\Entity;

interface ClubMember
{
    public function getClub(): ?Club;
    public function setClub(?Club $club): self;
}