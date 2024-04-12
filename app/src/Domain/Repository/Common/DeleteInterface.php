<?php

namespace App\Domain\Repository\Common;

interface DeleteInterface
{
    public function delete(object $entity): bool;
}