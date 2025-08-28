<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftHistory extends Model
{
    use HasFactory;
    protected $table = 'gift_history';

    protected $fillable = [
        'name',
        'address',
        'village_city',
        'district',
        'state',
        'type',
        'mobile_no',
        'vehicle_no',
        'used_reward_points',
        'employee',
    ];
    public function emp()
    {
        return $this->belongsTo(User::class, 'employee');
    }
}
