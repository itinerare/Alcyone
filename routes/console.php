<?php

use App\Console\Commands\ProcessCacheExpiry;
use Illuminate\Support\Facades\Schedule;

if (config('alcyone.settings.enable_backups')) {
    Schedule::command('backup:clean')
        ->daily()->at('01:30');
    Schedule::command('backup:run')
        ->daily()->at('01:00');
    Schedule::command('backup:monitor')
        ->daily()->at('01:40');
}

Schedule::command(ProcessCacheExpiry::class)
    ->everyMinute();
