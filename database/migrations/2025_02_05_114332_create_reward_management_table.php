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
        Schema::create('reward_management', function (Blueprint $table) {           
                $table->id();  // Primary key
                $table->string('vehicle_no')->nullable()->unique();  // Vehicle Number (unique for identification)
                $table->string('mobile_no')->nullable()->unique();  // Mobile Number (for contact)
                $table->decimal('total_amount', 10, 2);  // Total Amount (e.g., $999.99)
                $table->integer('earned_reward_points');  // Earned reward points (positive points)
                $table->integer('used_reward_points')->default(0);  // Used reward points (can be 0 initially)
                $table->integer('pending_reward_points')->default(0);  // Pending reward points (can be 0 initially)
                $table->timestamps();  // Created at & updated at timestamps        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_management');
    }
};
