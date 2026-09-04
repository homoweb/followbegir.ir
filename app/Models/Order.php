<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\Platform;
use App\Enums\ProductType;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $order_number
 * @property int|null $user_id
 * @property int $product_id
 * @property ProductType $product_type
 * @property Platform $product_platform
 * @property string $product_title
 * @property string $target_username
 * @property int $quantity
 * @property int $unit_price
 * @property int $total_price
 * @property OrderStatus $status
 * @property PaymentStatus $payment_status
 * @property Carbon|null $paid_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'order_number',
    'user_id',
    'product_id',
    'product_type',
    'product_platform',
    'product_title',
    'target_username',
    'quantity',
    'unit_price',
    'total_price',
    'status',
    'payment_status',
    'paid_at',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'product_type' => ProductType::class,
            'product_platform' => Platform::class,
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'paid_at' => 'datetime',
        ];
    }
}
