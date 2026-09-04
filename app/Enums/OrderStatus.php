<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Canceled = 'canceled';
    case Failed = 'failed';

    /**
     * Human readable (Persian) label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'در انتظار پرداخت',
            self::Processing => 'در حال انجام',
            self::Completed => 'تکمیل شده',
            self::Canceled => 'لغو شده',
            self::Failed => 'ناموفق',
        };
    }

    /**
     * Tailwind classes used by the badge component.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-500/10 text-amber-400 ring-amber-500/30',
            self::Processing => 'bg-sky-500/10 text-sky-400 ring-sky-500/30',
            self::Completed => 'bg-emerald-500/10 text-emerald-400 ring-emerald-500/30',
            self::Canceled => 'bg-zinc-500/10 text-zinc-400 ring-zinc-500/30',
            self::Failed => 'bg-rose-500/10 text-rose-400 ring-rose-500/30',
        };
    }
}
