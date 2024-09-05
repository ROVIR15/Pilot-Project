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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sales_identifier');
            $table->integer('goods_id');
            $table->string('warehouse_name');
            $table->date('date');
            $table->decimal('price', 10, 2);
            $table->integer('qty');
            $table->decimal('total', 10, 2);
            $table->string('buyer_name');
            $table->string('phone');
            $table->string('address');
            $table->text('note');
            $table->string('status');
            $table->date('shipping_date');
            $table->string('bill_of_lading');
            $table->string('coo');
            $table->string('qr_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('sales');
    }
};
