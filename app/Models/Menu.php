<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Menu extends Model
{
    protected $fillable = ['menuName', 'price', 'description', 'image', 'category', 'status'];

    public function variants(): HasMany
    {
        return $this->hasMany(MenuVariant::class);
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Inventory::class, 'menu_ingredients', 'menu_id', 'inventory_id')
                    ->withPivot('quantity_needed')
                    ->withTimestamps();
    }
}
