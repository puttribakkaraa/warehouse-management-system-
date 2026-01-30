<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingGood extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'supplier_id',
        'quantity',
        'remaining_quantity',
        'unit_price',
        'total_price',
        'date',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'remaining_quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Get the item for this incoming good
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the supplier for this incoming good
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Boot method to handle stock updates
     */
    protected static function boot()
    {
        parent::boot();

        // Set remaining quantity equal to quantity when creating, if not set
        static::creating(function ($incomingGood) {
            if (!isset($incomingGood->remaining_quantity)) {
                $incomingGood->remaining_quantity = $incomingGood->quantity;
            }
        });

        // Increase stock when incoming good is created
        static::created(function ($incomingGood) {
            $item = $incomingGood->item;
            $item->stock_quantity += $incomingGood->quantity;
            $item->save();
        });

        // Adjust stock when incoming good is deleted
        static::deleted(function ($incomingGood) {
            $item = $incomingGood->item;
            $item->stock_quantity -= $incomingGood->quantity;
            $item->save();
        });
    }
}
