<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

final readonly class AccrualEvent
{
    use Dispatchable;

    public function __construct(
        private int $userId,
        private int $amount,
    ) {
    }

    public function __invoke(): void
    {
    }
}
