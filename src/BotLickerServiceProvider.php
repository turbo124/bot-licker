<?php

namespace Turbo124\BotLicker;

use Turbo124\BotLicker\Rule;
use Turbo124\BotLicker\BotLicker;
use Illuminate\Support\ServiceProvider;
use Turbo124\BotLicker\Jobs\LogAnalysis;
use Illuminate\Console\Scheduling\Schedule;
use Turbo124\BotLicker\EventServiceProvider;
use Turbo124\BotLicker\Commands\FirewallShow;
use Turbo124\BotLicker\Commands\FirewallDbRules;
use Turbo124\BotLicker\Http\Middleware\QueryLog;

class BotLickerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/bot-licker.php' => config_path('bot-licker.php'),
            ], 'config');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                // FirewallRules::class,
                FirewallShow::class,
                FirewallDbRules::class
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

        if(config('bot-licker.enabled') && config('bot-licker.query_log'))
            $kernel->pushMiddleware(QueryLog::class);

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

        if(config('bot-licker.enabled') && config('bot-licker.query_log'))
            $this->app->singleton(QueryLog::class);

    }
}
