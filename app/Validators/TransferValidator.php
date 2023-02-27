<?php

namespace App\Validators;

use App\Exceptions\SelfTransferException;

class TransferValidator
{
    /**
     * @throws SelfTransferException
     */
    public function validateSelfTransfer(int $senderUserId, int $receiverUserId): void
    {
        if ($senderUserId === $receiverUserId) {
            throw new SelfTransferException();
        }
    }
}
