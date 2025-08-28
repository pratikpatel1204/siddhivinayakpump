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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('trx_id')->unique();
            $table->dateTime('date_time');
            $table->integer('pump');
            $table->integer('rdb_nozzle');
            $table->string('product');
            $table->decimal('unit_price', 10, 2);
            $table->string('payment');
            $table->decimal('volume', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->integer('print_id')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('mobile_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
