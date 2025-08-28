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
        Schema::create('daily_reward_expires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

            $table->date('reward_created_date');   // original purchase date
            $table->date('reward_expired_date');   // calculated expiry date
            $table->date('expired_on');            // actual cron job run date

            $table->decimal('expired_points', 10, 2)->default(0); // fixed syntax

            $table->timestamps();

            $table->unique(['customer_id', 'reward_expired_date']); // prevent duplicate expiry for same customer+date
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reward_expires');
    }
};