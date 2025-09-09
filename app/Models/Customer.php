<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'customers'; 
    protected $fillable = [
        'trx_id',
        'type',
        'date_time',
        'pump',
        'rdb_nozzle',
        'product',
        'unit_price',
        'payment',
        'volume',
        'amount',
        'print_id',
        'vehicle_no',
        'mobile_no',
        'reward_processed',
        'reward_expired_processed '
    ];
    public function dailyRewardExpires()
    {
        return $this->hasMany(DailyRewardExpire::class, 'customer_id');
    }
}