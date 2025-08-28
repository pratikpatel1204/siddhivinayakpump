<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyRewardExpire extends Model
{
    protected $fillable = [
        'customer_id',
        'reward_created_date',
        'reward_expired_date',
        'expired_on',
        'expired_points'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}