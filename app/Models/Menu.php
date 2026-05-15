<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = ['menuName', 'price', 'description', 'image'];

    public function variants(): HasMany
{
    return $this->hasMany(MenuVariant::class);
}
}
