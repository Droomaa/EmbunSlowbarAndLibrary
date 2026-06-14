<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $primaryKey = 'order_id';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['customer_name', 'table_number', 'total_price', 'status', 'payment_method', 'order_type'];
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id')->where('role', 'Admin');
    }

    public function staffShift()
    {
        return $this->belongsTo(StaffShift::class, 'staff_shift_id');
    }
}
