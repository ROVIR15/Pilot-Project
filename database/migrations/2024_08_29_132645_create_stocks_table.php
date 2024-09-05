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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('goods_id');
            $table->string('warehouse_name');
            $table->string('date')->default(date('Y-m-d'));
            $table->string('farmer_name')->nullable(true);
            $table->float('weight')->nullable(true);
            $table->string('grade')->nullable(true);
            $table->float('humidity')->nullable(true);
            $table->float('thickness')->nullable(true);
            $table->integer('qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
