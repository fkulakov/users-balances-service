<?php

namespace App\Jobs;

use App\Events\WriteOffEvent;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidAmountException;
use App\Exceptions\WriteOffException;
use App\Services\BalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class WriteOffJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly int $userId,
        private readonly int $writeOffAmount
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(BalanceService $balanceService): void
    {
        try {
            $balanceService->writeOff($this->userId, $this->writeOffAmount);
            dispatch(new WriteOffEvent($this->userId, $this->writeOffAmount));
        } catch (WriteOffException $writeOffException) {
            Log::error("Write off error: user_id {$this->userId}, amount {$this->writeOffAmount}");
        } catch (InsufficientBalanceException $insufficientBalanceException) {
            Log::error("Insufficient funds: user_id {$this->userId}, amount {$this->writeOffAmount}");
        } catch (InvalidAmountException $invalidAmountException) {
            Log::error("Invalid amount for write off: user_id {$this->userId}, amount {$this->writeOffAmount}");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Log::error("Attempt to write off {$this->writeOffAmount} amount from a nonexistent user_id {$this->userId}");
        } catch (\Throwable $exception) {
            Log::error(sprintf(
                'Unknown write off error: %s. user_id %d, amount %d',
                $exception->getMessage(),
                $this->userId,
                $this->writeOffAmount
            ));
        }
    }
}
