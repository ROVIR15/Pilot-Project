<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemConsumption extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_production_id',
        'identifier',
        'stock_id',
        'goods_id',
        'consumed_qty',
        'farmer_name',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
}
