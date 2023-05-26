<?php

namespace Turbo124\BotLicker;

use QueryLog;
use Turbo124\BotLicker\Facades\Rule;
use Illuminate\Support\ServiceProvider;
use Turbo124\BotLicker\Jobs\LogAnalysis;
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

                if(config('bot-licker.query_log'))
                    $schedule->job(new LogAnalysis())->everyFiveMinutes()->withoutOverlapping()->name('bot-licker-log-analysis-job')->onOneServer();

            });
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

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
        
        $this->app->bind('bot-licker-rule', function () {
            return new Rule();
        });

        $this->app->register(EventServiceProvider::class);

        if(config('bot-licker.query_log'))
            $this->app->singleton(QueryLog::class);

    }
}
