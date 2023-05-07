<?php

namespace Turbo124\BotLicker;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Turbo124\BotLicker\Facades\Firewall;

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
            ]);
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

    }
}
