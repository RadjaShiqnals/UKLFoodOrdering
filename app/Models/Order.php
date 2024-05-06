<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'customer_name',
        'table_number',
        'order_date'
    ];
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
