<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'status',
        'transaction_hash',
        'block_number',
        'farmer_name',
        'goods_id',
        'sales_id',
    ];
}
