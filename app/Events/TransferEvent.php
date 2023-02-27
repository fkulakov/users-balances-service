<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

final readonly class TransferEvent
{
    use Dispatchable;

    public function __construct(
        private int $senderUserId,
        private int $receiverUserId,
        private int $amount
    ) {
    }

    public function __invoke(): void
    {
    }
}
