<?php

namespace App\Validators;

use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\InvalidAmountException;

final readonly class AmountValidator
{
    /**
     * @throws InvalidAmountException
     */
    public function validateAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidAmountException();
        }
    }

    /**
     * @throws InsufficientBalanceException
     */
    public function validateWriteOffAmount(int $userBalance, int $writeOffAmount): void
    {
        if ($userBalance < $writeOffAmount) {
            throw new InsufficientBalanceException();
        }
    }
}
