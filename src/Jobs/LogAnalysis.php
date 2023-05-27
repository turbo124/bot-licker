<?php

namespace Turbo124\BotLicker\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Turbo124\BotLicker\Facades\Firewall;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Turbo124\BotLicker\Models\BotlickerBan;
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
        $rules = BotlickerRule::on(config('bot-licker.db_connection'))->get();
        
        BotlickerLog::on(config('bot-licker.db_connection'))
                    ->cursor()
                    ->each(function ($log) use ($rules){

                        if(BotlickerBan::where('ip', $log->ip)->doesntExist())
                        {
                            $rules->each(function ($rule) use ($log)
                            {
    
                                if (stripos($log->url, $rule->matches) !== false) 
                                {
                                    match ($rule->action) {
                                        'ban' => Firewall::ban($log->ip, $log->expires),
                                        'challenge' => Firewall::challenge($log->ip, $log->expires),
                                        default => null,
                                    };

                                    $log->delete();
                                    return;
                                }
                            });
                        }

                        $log->delete();

                    });

        BotlickerBan::on(config('bot-licker.db_connection'))
                    ->query()
                    ->where('expiry', '<', now())
                    ->cursor()
                    ->each(function ($ban){

                        if($ban->ip && $ban->action == 'ban'){
                            Firewall::unban($ban->ip);
                        }                        
                        if($ban->ip && $ban->action == 'challenge') {
                            Firewall::unchallenge($ban->ip);
                        }
                        elseif($ban->iso_3166_2 && $ban->action == 'ban'){
                            Firewall::unbanCountry($ban->iso_3166_2);
                        }
                        elseif($ban->iso_3166_2 && $ban->action == 'challenge'){
                            Firewall::unbanCountry($ban->iso_3166_2);
                        }

                    });
    }

    public function middleware()
    {
        return ([new WithoutOverlapping(1)]);
    }

}
