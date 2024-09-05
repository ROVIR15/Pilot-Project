<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsLog extends Model
{
    use HasFactory;

    protected $title = 'goods_log';

    protected $fillable = [
        'date',
        'stock_id',
        'goods_id',
        'fg_id',
        'farmer_name',
        'qty',
        'type_movement',
        'warehouse_name',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class)->with('goods');
    }
}
