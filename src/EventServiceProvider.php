<?php

namespace Turbo124\BotLicker;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

    ];



    // /**
    //  * {@inheritdoc}
    //  */
    // public function __construct(Application $app)
    // {
    //     parent::__construct($app);

    //     $this->listen = [];
    // }


    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        
        $this->listen = (array)config('bot-licker.events');

    }
}
