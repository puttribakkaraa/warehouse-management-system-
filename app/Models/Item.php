<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'stock_quantity',
        'minimum_stock',
        'description',
    ];

    protected $casts = [
        'stock_quantity' => 'decimal:2',
        'minimum_stock' => 'decimal:2',
    ];

    /**
     * Get incoming goods for this item
     */
    public function incomingGoods(): HasMany
    {
        return $this->hasMany(IncomingGood::class);
    }

    /**
     * Get outgoing goods for this item
     */
    public function outgoingGoods(): HasMany
    {
        return $this->hasMany(OutgoingGood::class);
    }

    /**
     * Get menus that use this item
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'menu_ingredients')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->minimum_stock;
    }

    /**
     * Get stock status
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'habis';
        } elseif ($this->stock_quantity <= $this->minimum_stock) {
            return 'hampir_habis';
        }
        return 'aman';
    }
}
