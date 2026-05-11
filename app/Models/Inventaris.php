<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    // Nama tabelnya manual karena Laravel biasanya nyari 'inventarises'
    protected $table = 'inventaris';

    // Kolom yang boleh diisi lewat create/update massal
    protected $fillable = ['itemName', 'stock', 'satuan'];
}
