<?php

namespace App\Domain\Repository;

interface NotifierInterface
{
    public function notify(string $recipientAddress, string $senderAddress, string $subject, string $body): bool;
}