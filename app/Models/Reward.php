<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;
    protected $table = 'rewards'; 
    protected $fillable = [
        'regular_reward_price', 
        'regular_reward_points', 
        'commercial_reward_price', 
        'commercial_reward_points', 
        'tractor_reward_price', 
        'tractor_reward_points', 
        'regular_price', 
        'regular_gift_point', 
        'commercial_price', 
        'commercial_gift_point', 
        'tractor_price', 
        'tractor_gift_point', 
        'expiry_days'
    ];
}
