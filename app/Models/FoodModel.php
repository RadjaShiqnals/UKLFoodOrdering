<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodModel extends Model
{
    protected $table = 'food';
    protected $primaryKey = 'id_food';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'spicy_level',
        'price',
        'image'
    ];
}
