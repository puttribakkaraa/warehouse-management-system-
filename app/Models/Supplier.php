<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'notes',
    ];

    /**
     * Get incoming goods from this supplier
     */
    public function incomingGoods(): HasMany
    {
        return $this->hasMany(IncomingGood::class);
    }

    /**
     * Get bills for this supplier
     */
    public function bills(): HasMany
    {
        return $this->hasMany(SupplierBill::class);
    }

    /**
     * Get total unpaid bills
     */
    public function getTotalUnpaidAttribute(): float
    {
        return $this->bills()->where('status', 'belum_lunas')->sum('total_amount');
    }
}
