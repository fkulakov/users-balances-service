<?php

namespace App\Console\Commands;

use App\Jobs\AccrualJob;
use Illuminate\Console\Command;

class ProduceAccrualJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:produce-accrual
                            {user_id : user_id for accrual money}
                            {accrual_amount : amount of money}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produce AccrualJob';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userId = (int) $this->argument('user_id');
        $accrualAmount = (int) $this->argument('accrual_amount');

        dispatch(new AccrualJob($userId, $accrualAmount));
    }
}
