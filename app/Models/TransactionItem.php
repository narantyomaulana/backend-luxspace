<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'users_id', 
        'products_id', 
        'transactions_id', 
        
    ];

    public function products()
    {
        return $this->hashOne(Products::class, 'id', 'products_id');
    }
}
