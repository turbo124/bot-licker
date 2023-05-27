<?php

namespace Turbo124\BotLicker\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Turbo124\BotLicker\Facades\Firewall;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Turbo124\BotLicker\Models\Botlicker as BotModel;
use Turbo124\BotLicker\Models\BotlickerLog;
use Turbo124\BotLicker\Models\BotlickerRule;
use Illuminate\Queue\Middleware\WithoutOverlapping;

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
        $rules = BotlickerRule::on("database.".config('bot-licker.db_connection'))->all();
        
        BotlickerLog::on("database.".config('bot-licker.db_connection'))
                    ->query()
                    ->cursor()
                    ->each(function ($log) use ($rules){

                        if(BotModel::where('ip', $log->ip)->doesntExist())
                        {
                            $rules->each(function ($rule) use ($log)
                            {
    
                                if (stripos($log->uri, $rule->matches) !== false) 
                                {
                                    match ($rule->action) {
                                        'ban' => Firewall::ban($log->ip)->expiry($log->expires),
                                        'challenge' => Firewall::challenge($log->ip)->expiry($log->expires),
                                        default => null,
                                    };

                                    $log->delete();
                                    return;
                                }
                            });
                        }

                        $log->delete();

                    });
    }

    public function middleware()
    {
        return ([new WithoutOverlapping(1)]);
    }

}
