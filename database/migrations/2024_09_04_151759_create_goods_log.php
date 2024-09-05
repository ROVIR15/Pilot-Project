<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('GoodsLog', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('stock_id');
            $table->integer('goods_id');
            $table->string('farmer_name');
            $table->integer('qty');
            $table->string('type_movement');
            $table->string('warehouse_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goods_log');
    }
};
