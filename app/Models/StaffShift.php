<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shift_date',
        'started_at',
        'ended_at',
        'status',
        'check_in_note',
        'check_out_note',
        'opening_cash',
        'closing_cash',
        'total_orders',
        'total_sales',
        'duration_minutes',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'shift_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
