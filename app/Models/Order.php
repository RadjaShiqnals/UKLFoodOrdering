<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'customer_name', // Nama pelanggan
        'table_number', // Nomor meja
        'order_date' // Tanggal pemesanan
    ];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
