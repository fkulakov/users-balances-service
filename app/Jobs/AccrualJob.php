<?php

namespace App\Jobs;

use App\Events\AccrualEvent;
use App\Exceptions\AccrualException;
use App\Exceptions\InvalidAmountException;
use App\Services\BalanceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

final class AccrualJob implements ShouldQueue
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
        private readonly int $accrualAmount
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(BalanceService $balanceService): void
    {
        try {
            $balanceService->accrual($this->userId, $this->accrualAmount);
            dispatch(new AccrualEvent($this->userId, $this->accrualAmount));
        } catch (AccrualException $accrualException) {
            Log::error("Accrual error: user_id {$this->userId}, amount {$this->accrualAmount}");
        } catch (InvalidAmountException $invalidAmountException) {
            Log::error("Invalid amount for accrual: user_id {$this->userId}, amount {$this->accrualAmount}");
        } catch (ModelNotFoundException $modelNotFoundException) {
            Log::error("Attempt to accrual {$this->accrualAmount} to a nonexistent user_id {$this->userId}");
        } catch (\Throwable $exception) {
            Log::error(sprintf(
                'Unknown accrual error: %s. user_id %d, amount %d',
                $exception->getMessage(),
                $this->userId,
                $this->accrualAmount
            ));
        }
    }
}
