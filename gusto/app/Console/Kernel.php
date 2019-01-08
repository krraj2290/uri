<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
//        "App\Console\Commands\ChannelSubscribe",
//        "App\Console\Commands\RohumaSubscribe",
        "App\Console\Commands\SubscribeDefault",
        "App\Console\Commands\SubscribeRohuma",
        'App\Console\Commands\CallRoute',
        'App\Console\Commands\RohumaCallRoute',
        'App\Console\Commands\QueueDefaultSnaplionEventTrack',
        'App\Console\Commands\QueueDefaultEvent',
        'App\Console\Commands\QueueRohumaEvent',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
