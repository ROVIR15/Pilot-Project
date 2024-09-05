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
        // add column of user in goods table
        Schema::table('goods', function (Blueprint $table) {
            $table->integer('user_id')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // drop column of user in goods table
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
