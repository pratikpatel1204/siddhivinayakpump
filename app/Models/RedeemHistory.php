<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedeemHistory extends Model
{
    use HasFactory;
    protected $table = 'redeem_history';

    protected $fillable = [
        'reward_management_id',
        'name',
        'address',
        'village_city',
        'district',
        'state',
        'type',
        'mobile_no',
        'vehicle_no',
        'service',
        'used_reward_points',
        'employee',
    ];

    public function rewardManagement()
    {
        return $this->belongsTo(RewardManagement::class, 'reward_management_id');
    }
    public function emp()
    {
        return $this->belongsTo(User::class, 'employee');
    }
}
