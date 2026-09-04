<?php

namespace App\Enums;

enum PaymentCallbackStatus: string
{
    case Success = 'success';
    case AlreadyVerified = 'already_verified';
    case Canceled = 'canceled';
    case Failed = 'failed';
    case Unknown = 'unknown';

    /**
     * Whether the payment can be considered paid.
     */
    public function isPaid(): bool
    {
        return $this === self::Success || $this === self::AlreadyVerified;
    }
}
