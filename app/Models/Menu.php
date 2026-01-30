<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get ingredients for this menu
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'menu_ingredients')
            ->withPivot('quantity_required')
            ->withTimestamps();
    }

    /**
     * Get outgoing goods related to this menu
     */
    public function outgoingGoods(): HasMany
    {
        return $this->hasMany(OutgoingGood::class);
    }
}
