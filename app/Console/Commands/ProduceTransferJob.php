<?php

namespace App\Console\Commands;

use App\Jobs\TransferJob;
use Illuminate\Console\Command;

class ProduceTransferJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:produce-transfer
                            {sender_user_id : sender user_id}
                            {receiver_user_id : receiver user_id}
                            {transfer_amount : amount of money}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Produce TransferJob';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $senderUserId = (int) $this->argument('sender_user_id');
        $receiverUserId = (int) $this->argument('receiver_user_id');
        $transferAmount = (int) $this->argument('transfer_amount');

        dispatch(new TransferJob($receiverUserId, $senderUserId, $transferAmount));
    }
}
