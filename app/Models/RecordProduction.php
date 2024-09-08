<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordProduction extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier',
        'goods_id',
        'date',
        'qty',
    ];

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    // has many item consumption
    public function item_consumptions()
    {
        return $this->hasMany(ItemConsumption::class);
    }
}
