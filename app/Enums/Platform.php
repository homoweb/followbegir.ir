<?php

namespace App\Enums;

enum Platform: string
{
    case Instagram = 'instagram';

    /**
     * Human readable (Persian) label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Instagram => 'اینستاگرام',
        };
    }
}
