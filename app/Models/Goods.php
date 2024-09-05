<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'ptype', 'qty', 'description', 'qr_url', 'user_id'];

    public function stock()
    {
        return $this->hasMany(Stock::class, 'goods_id', 'id');
    }
}
