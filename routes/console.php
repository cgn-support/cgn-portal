<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // Import Schedule
use App\Console\Commands\SyncMondayUserPhotos; // Import your command

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Define your scheduled tasks here
Schedule::command(SyncMondayUserPhotos::class) // You can use the class name directly
    ->dailyAt('02:00')
    ->withoutOverlapping(10) // Prevent overlapping if the job takes time
    ->onFailure(function () {
        // Optional: Notify on failure
        \Illuminate\Support\Facades\Log::channel('slack')->error('Scheduled Monday User Photo Sync failed!');
        // Or use your preferred logging/notification method
    });

// You can also use the command signature:
// Schedule::command('monday:sync-user-photos')
//          ->dailyAt('02:00');

// Add other scheduled commands here
