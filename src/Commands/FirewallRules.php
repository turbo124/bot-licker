<?php

namespace Turbo124\BotLicker\Commands;

use Illuminate\Console\Command;
use Turbo124\BotLicker\BotLicker;

class FirewallRules extends Command
{
    /**
     * @var string
     */
    protected $name = 'firewall:cf-rules';

    /**
     * @var string
     */
    protected $description = 'List Firewall Rules';

    public function handle()
    {

        $bot = new BotLicker();

        $rules = $bot->getRules();
          
            $block = $rules[0]['block'];
            $challenge = $rules[1]['managed_challenge'];

            $table = [];

            for($x=0; $x < (max(count($block), count($challenge))); $x++)
            {
                $table[] = [
                    $block[$x] ?? '',
                    $challenge[$x] ?? '',
                ];
            }

        $this->table(
            ['Block', 'Challenge'],
            $table
        );

    }
}
