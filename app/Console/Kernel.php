<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('rewardspoint:expired')->everyMinute();
    }

    protected $commands = [
        \App\Console\Commands\rewardspointexpired::class,
    ];
}
