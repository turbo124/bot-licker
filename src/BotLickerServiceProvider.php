<?php

namespace Turbo124\BotLicker;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Turbo124\BotLicker\EventServiceProvider;
use Turbo124\BotLicker\Commands\FirewallRules;

class BotLickerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/bot-licker.php' => config_path('bot-licker.php'),
            ], 'config');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                FirewallRules::class
            ]);
        }


        if ($this->app->runningInConsole()) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);
                // $schedule->job(new BatchMetrics())->everyFiveMinutes()->withoutOverlapping()->name('beacon-batch-job')->onOneServer();
            });
        }


    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/bot-licker.php', 'bot-licker');

        $this->app->bind('bot-licker', function () {
            return new BotLicker();
        });

        $this->app->register(EventServiceProvider::class);

    }
}
