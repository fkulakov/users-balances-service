<?php

namespace App\Console\Commands;

use App\Jobs\WriteOffJob;
use Illuminate\Console\Command;

class ProduceWriteOffJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:produce-write-off
                            {user_id : user_id for write off money}
                            {write_off_amount : amount of money}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produce WriteOffJob';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userId = (int) $this->argument('user_id');
        $writeOffAmount = (int) $this->argument('write_off_amount');

        dispatch(new WriteOffJob($userId, $writeOffAmount));
    }
}
