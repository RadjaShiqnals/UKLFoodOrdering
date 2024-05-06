<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';

    protected $fillable = [
        'name', // Nama pelanggan
        'email', // Email pelanggan
        'phone', // Nomor telepon pelanggan
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
