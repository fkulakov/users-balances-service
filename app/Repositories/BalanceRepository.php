<?php

namespace App\Repositories;

use App\Models\Balance;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

final readonly class BalanceRepository
{
    /**
     * @throws ModelNotFoundException
     */
    public function getBalance(int $userId): Balance
    {
        /** @var Balance $balance */
        $balance = Balance::query()
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->firstOrFail();

        return $balance;
    }

    public function writeOff(int $userId, int $amount): bool
    {
        $updatedBalances = Balance::query()
            ->where('user_id', $userId)
            ->where('amount', '>=', $amount)
            ->update(['amount' => DB::raw("amount - $amount")]);

        return $updatedBalances > 0;
    }

    public function accrual(int $userId, int $amount): bool
    {
        $updatedBalances = Balance::query()
            ->where('user_id', $userId)
            ->update(['amount' => DB::raw("amount + $amount")]);

        return $updatedBalances > 0;
    }
}
