<?php

namespace App\Jobs;

use App\Events\TransferEvent;
use App\Exceptions\AccrualException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidAmountException;
use App\Exceptions\SelfTransferException;
use App\Exceptions\WriteOffException;
use App\Services\BalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class TransferJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly int $senderUserId,
        private readonly int $receiverUserId,
        private readonly int $transferAmount
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(BalanceService $balanceService): void
    {
        try {
            $balanceService->transfer($this->senderUserId, $this->receiverUserId, $this->transferAmount);
            dispatch(new TransferEvent($this->senderUserId, $this->receiverUserId, $this->transferAmount));
        } catch (InsufficientBalanceException $accrualException) {
            Log::error(sprintf(
                'Transfer error: insufficient funds. Sender user_id %d, receiver user_id %d, amount %d',
                $this->senderUserId,
                $this->receiverUserId,
                $this->transferAmount,
            ));
        } catch (AccrualException $accrualException) {
            Log::error(sprintf(
                'Transfer error: failed to accrual %d amount to %d user_id. Sender user_id: %d',
                $this->transferAmount,
                $this->receiverUserId,
                $this->senderUserId,
            ));
        } catch (InvalidAmountException $invalidAmountException) {
            Log::error(sprintf(
                'Transfer error: invalid amount %d. Sender user_id %d, receiver user_id %d',
                $this->transferAmount,
                $this->senderUserId,
                $this->receiverUserId,
            ));
        } catch (SelfTransferException $selfTransferException) {
            Log::error(sprintf(
                'Transfer error: user_id %s is trying to send %d amount to himself',
                $this->senderUserId,
                $this->transferAmount,
            ));
        } catch (WriteOffException $writeOffException) {
            Log::error(sprintf(
                'Transfer error: failed to write off %d amount from %d user_id. Receiver user_id: %d',
                $this->transferAmount,
                $this->senderUserId,
                $this->receiverUserId,
            ));
        } catch (\Throwable $exception) {
            Log::error(sprintf(
                'Unexpected transfer error: %s. Sender user_id %d, receiver user_id %d, amount %d',
                $exception->getMessage(),
                $this->senderUserId,
                $this->receiverUserId,
                $this->transferAmount,
            ));
        }
    }
}
