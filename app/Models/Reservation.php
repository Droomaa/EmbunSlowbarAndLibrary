<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $primaryKey = 'reservation_id';
    protected $fillable = [
        'customer_name',
        'phone_number',
        'reservation_date',
        'pax',
        'status',
        'notes'
    ];
}
