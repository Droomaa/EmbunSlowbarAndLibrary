<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['customer_id', 'date', 'startTime', 'duration', 'jumlahOrang', 'status'];
}
