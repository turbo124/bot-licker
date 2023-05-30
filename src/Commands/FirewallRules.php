<?php

namespace Turbo124\BotLicker\Commands;

use Illuminate\Console\Command;
use Turbo124\BotLicker\BotLicker;
use Turbo124\BotLicker\Facades\Firewall;

class FirewallRules extends Command
{
    /**
     * @var string
     */
    protected $signature = 'firewall:cf-rules {--ban} {--challenge} {--unban} {--unchallenge}';

    /**
     * @var string
     */
    protected $description = 'List Firewall Rules';

    public function handle()
    {
        
        if ($this->option('ban')) {

            if(strlen($this->option('ban') == 2))
                Firewall::banCountry($this->option('ban'));
            else
                Firewall::ban($this->option('ban'));

            $this->info("Ban added for ".$this->option('ban'));
        }

        if ($this->option('challenge')) {

            Firewall::challenge($this->option('challenge'));

            $this->info("Challenge added for ".$this->option('challenge'));

        }

        if ($this->option('unban')) {
            if (strlen($this->option('unban') == 2)) {
                Firewall::unbanCountry($this->option('unban'));
            } else {
                Firewall::unban($this->option('unban'));
            }

            $this->info("Unbanned ".$this->option('unban'));
        }

        if ($this->option('unchallenge')) {
                Firewall::unchallenge($this->option('unchallenge'));
            $this->info("Challenge removed for ".$this->option('unchallenge'));
        }

        $bot = new BotLicker();

        $rules = $bot->getRules();

        $block = $rules[0]['block'] ?? [];
        $challenge = $rules[1]['managed_challenge'] ?? [];

        $table = [];

        for($x=0; $x < (max(count($block), count($challenge))); $x++)
        {
            $table[] = [
                $block[$x] ?? '',
                $challenge[$x] ?? '',
            ];
        }

        echo print_r(
$this->table(
    ['Block', 'Challenge'],
    $table
)
)
        $this->table(
            ['Block', 'Challenge'],
            $table
        );

    }
}
