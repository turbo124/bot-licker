<?php

namespace Turbo124\BotLicker\Commands;

use Illuminate\Console\Command;
use Turbo124\BotLicker\Facades\Firewall;
use Turbo124\BotLicker\Models\BotlickerBan;

class FirewallShow extends Command
{
    /**
     * @var string
     */
    protected $name = 'firewall:show --{delete=}';

    /**
     * @var string
     */
    protected $description = 'Show actioned IP/Countries';

    public function handle()
    {
        if($this->option('delete')) {
            $bb = BotlickerBan::on(config('bot-licker.db_connection'))->find($this->option('delete'));

            if($bb) {
             
                if($bb->ip){
                    Firewall::unban($bb->ip);
                }

                if($bb->iso_3166_2){
                    Firewall::unbanCountry($bb->iso_3166_2);
                }

                $bb->delete();

                $this->info("Deleted Ban, and removed from firewall");
                return;
            }

            $this->info('I could not find that ban.');
        }

        $this->table(
            ['id', 'ip', 'country', 'expiry'],
            BotlickerBan::all(['id', 'ip', 'iso_3166_2', 'expiry'])->toArray()
        );
    }
}
