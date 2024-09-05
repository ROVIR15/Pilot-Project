<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'sales_identifier'];

    protected $fillable = [
        'sales_identifier',
        'goods_id',
        'warehouse_name',
        'date',
        'price',
        'qty',
        'total',
        'buyer_name',
        'phone',
        'address',
        'note',
        'status',
        'shipping_date',
        'bill_of_lading',
        'coo',
        'qr_url',
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }
}
