<?php

namespace App\Enums;

enum PaymentTxnStatus: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';
    case Canceled = 'canceled';

    /**
     * Human readable (Persian) label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'در انتظار نتیجه',
            self::Success => 'موفق',
            self::Failed => 'ناموفق',
            self::Canceled => 'لغو شده',
        };
    }
}
