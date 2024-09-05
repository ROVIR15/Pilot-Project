<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufactureSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_id',
        'consumption',
    ];

    // goods

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
}
