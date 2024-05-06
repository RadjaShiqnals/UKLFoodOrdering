<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodModel extends Model
{
    protected $table = 'food';
    protected $primaryKey = 'id_food';
    public $timestamps = true;

    protected $fillable = [
        'name', // Nama makanan
        'spicy_level', // Tingkat kepedasan makanan
        'price', // Harga makanan
        'image' // Gambar makanan
    ];
}
