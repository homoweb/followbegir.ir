<?php

namespace App\Enums;

enum ProductType: string
{
    case Followers = 'followers';
    case Likes = 'likes';

    /**
     * Human readable (Persian) label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Followers => 'فالوور',
            self::Likes => 'لایک',
        };
    }
}
