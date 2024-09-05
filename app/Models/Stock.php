<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_id',
        'warehouse_name',
        'date',
        'stock_type',
        'farmer_name',
        'weight',
        'grade',
        'humidity',
        'thickness',
        'qty',
    ];

    // get product info from product model
    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id', 'id');
    }
}
