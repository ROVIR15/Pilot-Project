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
        Schema::create('record_productions', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->string('goods_id');
            $table->date('date');
            $table->integer('qty');
            $table->timestamps();
        });

        Schema::create('item_consumptions', function (Blueprint $table) {
            $table->id();
            $table->integer('record_production_id');
            $table->string('identifier');
            $table->string('stock_id');
            $table->string('goods_id');
            $table->integer('consumed_qty');
            $table->string('farmer_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_productions');
        Schema::dropIfExists('item_consumptions');
    }
};
