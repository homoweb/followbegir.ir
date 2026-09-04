<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';

    /**
     * Human readable (Persian) label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'پرداخت نشده',
            self::Paid => 'پرداخت شده',
            self::Failed => 'پرداخت ناموفق',
            self::Refunded => 'بازگشت وجه',
        };
    }

    /**
     * Tailwind classes used by the badge component.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Unpaid => 'bg-amber-500/10 text-amber-400 ring-amber-500/30',
            self::Paid => 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/30',
            self::Failed => 'bg-rose-500/10 text-rose-400 ring-rose-500/30',
            self::Refunded => 'bg-violet-500/10 text-violet-400 ring-violet-500/30',
        };
    }
}
