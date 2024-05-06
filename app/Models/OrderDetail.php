<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_details';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'order_id', // ID pesanan
        'food_id', // ID makanan
        'quantity', // Jumlah makanan
        'price' // Harga makanan
    ];
}
