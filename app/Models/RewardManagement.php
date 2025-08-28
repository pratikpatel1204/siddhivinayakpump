<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardManagement extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'reward_management';

    // Define which attributes can be mass-assigned
    protected $fillable = [
        'type',
        'vehicle_no',
        'mobile_no',
        'total_amount',
        'earned_reward_points',
        'used_reward_points',
        'pending_reward_points',
    ];

    // Optionally, define any castings (e.g., for dates or decimals)
    protected $casts = [
        'total_amount' => 'decimal:2', // Cast total_amount to a decimal with 2 places
        'earned_reward_points' => 'integer',
        'used_reward_points' => 'integer',
        'pending_reward_points' => 'integer',
    ];
}
