<?php

namespace App\Models;

use App\Enums\PaymentTxnStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property string $uuid
 * @property int $order_id
 * @property int|null $user_id
 * @property int $amount
 * @property string $gateway
 * @property string|null $authority
 * @property string|null $reference_id
 * @property string|null $card_number
 * @property PaymentTxnStatus $status
 * @property string|null $gateway_response
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'uuid',
    'order_id',
    'user_id',
    'amount',
    'gateway',
    'authority',
    'reference_id',
    'card_number',
    'status',
    'gateway_response',
    'paid_at',
])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $payment): void {
            $payment->uuid ??= (string) Str::uuid();
        });
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => PaymentTxnStatus::class,
            'paid_at' => 'datetime',
        ];
    }
}
