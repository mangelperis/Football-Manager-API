<?php

namespace App\Domain\Repository\Common;

interface UpdateInterface
{
    public function update(string $attribute, mixed $value, object $entity): ?object;
}