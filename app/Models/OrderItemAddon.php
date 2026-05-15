<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemAddon extends Model
{
    protected $fillable = ['order_item_id', 'add_on_id', 'price'];

    // Relasi ke order_items
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id', 'item_id');
    }

    // Relasi ke tabel add_ons master
    public function addOn(): BelongsTo
    {
        return $this->belongsTo(AddOn::class);
    }
}
