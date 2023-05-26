<?php

namespace Turbo124\BotLicker\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Turbo124\BotLicker\Models\BotlickerLog;
use Turbo124\BotLicker\Models\BotlickerRule;

class LogAnalysis implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $rules = BotlickerRule::all();
        
        BotlickerLog::query()
                    ->cursor()
                    ->each(function ($log) use ($rules){

                        if(stripos($log->uri, $rules->matches) !== false)

                        $log->delete();
                        
                    });
    }

    public function middleware()
    {
        return ([new WithoutOverlapping(1)]);
    }

}
