<?php

namespace App\Domain\Repository\Common;

interface SaveInterface
{
    public function save(object $entity): bool;
}