<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingGood extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'menu_id',
        'quantity',
        'date',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'date' => 'date',
    ];

    /**
     * Get the item for this outgoing good
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the menu for this outgoing good
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Boot method to handle stock updates
     */
    /**
     * Boot method to handle stock updates
     */
    protected static function boot()
    {
        parent::boot();

        // Decrease stock when outgoing good is created
        static::created(function ($outgoingGood) {
            static::deductStock($outgoingGood);
        });

        // Restore stock when outgoing good is deleted
        static::deleted(function ($outgoingGood) {
            static::restoreStock($outgoingGood);
        });
    }

    /**
     * Decrease global and batch stock (FIFO)
     */
    public static function deductStock($outgoingGood)
    {
        // 1. Decrease Global Stock
        // Always fetch fresh item to ensure stock_quantity is up to date
        $item = Item::find($outgoingGood->item_id);
        
        if ($item) {
            $item->stock_quantity -= $outgoingGood->quantity;
            $item->save();
        }

        // 2. FIFO: Decrease Batch Stock (Incoming Goods)
        $qtyToDeduct = $outgoingGood->quantity;
        
        $batches = \App\Models\IncomingGood::where('item_id', $outgoingGood->item_id)
            ->where('remaining_quantity', '>', 0)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($batches as $batch) {
            if ($qtyToDeduct <= 0) break;

            if ($batch->remaining_quantity >= $qtyToDeduct) {
                $batch->remaining_quantity -= $qtyToDeduct;
                $batch->save();
                $qtyToDeduct = 0;
            } else {
                $qtyToDeduct -= $batch->remaining_quantity;
                $batch->remaining_quantity = 0;
                $batch->save();
            }
        }
    }

    /**
     * Restore global and batch stock (LIFO)
     */
    public static function restoreStock($outgoingGood)
    {
        // 1. Restore Global Stock
        // Always fetch fresh item to ensure stock_quantity is up to date
        $item = Item::find($outgoingGood->item_id);

        if ($item) {
            $item->stock_quantity += $outgoingGood->quantity;
            $item->save();
        }

        // 2. LIFO Restoration: Fill back the newest batches first
        $qtyToRestore = $outgoingGood->quantity;

        $batches = \App\Models\IncomingGood::where('item_id', $outgoingGood->item_id)
            ->whereColumn('remaining_quantity', '<', 'quantity')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        foreach ($batches as $batch) {
            if ($qtyToRestore <= 0) break;

            $spaceAvailable = $batch->quantity - $batch->remaining_quantity;
            
            if ($spaceAvailable > 0) {
                if ($qtyToRestore <= $spaceAvailable) {
                    $batch->remaining_quantity += $qtyToRestore;
                    $qtyToRestore = 0;
                } else {
                    $batch->remaining_quantity += $spaceAvailable;
                    $qtyToRestore -= $spaceAvailable;
                }
                $batch->save();
            }
        }
    }
}
