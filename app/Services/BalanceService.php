<?php

namespace App\Services;

use App\Exceptions\AccrualException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidAmountException;
use App\Exceptions\SelfTransferException;
use App\Exceptions\WriteOffException;
use App\Repositories\BalanceRepository;
use App\Validators\AmountValidator;
use App\Validators\TransferValidator;
use Illuminate\Support\Facades\DB;

final readonly class BalanceService
{
    public function __construct(
        private BalanceRepository $repository,
        private AmountValidator $amountValidator,
        private TransferValidator $transferValidator
    ) {
    }

    /**
     * @throws InvalidAmountException
     * @throws WriteOffException
     * @throws InsufficientBalanceException
     */
    public function writeOff(int $userId, int $writeOffAmount): void
    {
        $this->amountValidator->validateAmount($writeOffAmount);
        $userBalance = $this->repository->getBalance($userId);
        $this->amountValidator->validateWriteOffAmount($userBalance->amount, $writeOffAmount);

        if (!$this->repository->writeOff($userId, $writeOffAmount)) {
            throw new WriteOffException();
        }
    }

    /**
     * @throws AccrualException
     * @throws InvalidAmountException
     */
    public function accrual(int $userId, int $accrualAmount): void
    {
        $this->amountValidator->validateAmount($accrualAmount);
        $this->repository->getBalance($userId);

        if (!$this->repository->accrual($userId, $accrualAmount)) {
            throw new AccrualException();
        }
    }

    /**
     * @throws AccrualException
     * @throws WriteOffException
     * @throws InvalidAmountException
     * @throws SelfTransferException
     * @throws InsufficientBalanceException
     * @throws \Throwable
     */
    public function transfer(int $senderUserId, int $recipientUserId, int $transferAmount): void
    {
        $this->amountValidator->validateAmount($transferAmount);
        $this->transferValidator->validateSelfTransfer($senderUserId, $recipientUserId);
        $senderBalance = $this->repository->getBalance($senderUserId);
        $this->amountValidator->validateWriteOffAmount($senderBalance->amount, $transferAmount);
        $this->repository->getBalance($recipientUserId);

        try {
            DB::beginTransaction();
            $this->writeOff($senderUserId, $transferAmount);
            $this->accrual($recipientUserId, $transferAmount);
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
