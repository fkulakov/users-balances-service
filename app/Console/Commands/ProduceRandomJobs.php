<?php

namespace App\Console\Commands;

use App\Jobs\AccrualJob;
use App\Jobs\TransferJob;
use App\Jobs\WriteOffJob;
use App\Models\Balance;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ProduceRandomJobs extends Command
{
    protected $signature = 'jobs:produce-random
                            {count : count of random jobs}';

    protected $description = 'Produce multiple test jobs';

    public function handle(): void
    {
        $balances = Balance::all(['user_id'])->toArray();
        $availableJobs = [
            AccrualJob::class,
            TransferJob::class,
            WriteOffJob::class,
        ];

        for ($i = 0; $i < (int) $this->argument('count'); ++$i) {
            $amount = random_int(1, 1000);
            $job = Arr::random($availableJobs);

            if (TransferJob::class === $job) {
                $senderUserId = Arr::random($balances)['user_id'];
                $receiverUserId = Arr::random($balances)['user_id'];

                $job = new $job($senderUserId, $receiverUserId, $amount);
            } else {
                $userId = Arr::random($balances)['user_id'];
                $job = new $job($userId, $amount);
            }

            dispatch($job);
        }
    }
}
