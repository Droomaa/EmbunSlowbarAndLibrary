<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $primaryKey = 'item_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['order_id', 'menu_id', 'quantity', 'subtotal'];
    public function variant(): BelongsTo
{
    return $this->belongsTo(MenuVariant::class, 'menu_variant_id');
}

public function addOns(): HasMany
{
    // Relasi ke tabel pivot yang mencatat add-on per item
    return $this->hasMany(OrderItemAddon::class, 'order_item_id', 'item_id');
}

public function menu(): BelongsTo
{
    return $this->belongsTo(Menu::class, 'menu_id', 'id');
}
}
