<?php

namespace Turbo124\BotLicker\Tests;

use Turbo124\BotLicker\BotLicker;
use Turbo124\BotLicker\Facades\Firewall;

class ConfigTest extends BotLickerTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }
    
    public function testValidInstanceType()
    {
            $firewall = new Firewall();
            $this->assertTrue($firewall instanceof Firewall);
    }


    // public function testPrintRules()
    // {

    //     Application::starting(function ($artisan) {
    //         $artisan->add(app(FirewallRules::class));
    //     });

    //     $output = new BufferedOutput();
        
    //     $x = Artisan::call('firewall:cf-rules --ban --challenge --unban --unchallenge', [], $output);
        
    //     echo print_r($output->fetch(),1);

    //     // $cp = new CloudflareProvider();
        
    //     // echo print_r($cp->challengeIp('192.168.0.129'),1);

    //     // echo print_r($cp->getRules());
        
    // }

    /** @test */
    

    public function testAddIp()
    {

        $bot = new BotLicker();
        $result = $bot->ban('101.55.31.19');

        $this->assertTrue($result->action_status);
    }

    public function testRemoveIp()
    {
        $bot = new BotLicker();
        $result = $bot->unban('101.55.31.19');

        $this->assertTrue($result->action_status);
    }

    public function testAddChallengeIP()
    {
        $bot = new BotLicker();
        $result = $bot->challenge('101.55.32.119');

        $this->assertTrue($result->action_status);
    }

    public function testRemoveChallengeIP()
    {
        $bot = new BotLicker();
        $result = $bot->unchallenge('101.55.32.119');

        $this->assertTrue($result->action_status);
    }

    public function testRemoveChallengeIP2()
    {
        $bot = new BotLicker();
        $result = $bot->unchallenge('101.55.32.120');

        $this->assertTrue($result->action_status);
    }

    public function testAddCountryBan()
    {
        $bot = new BotLicker();
        $result = $bot->banCountry('DE');

        $this->assertTrue($result->action_status);
    }

    public function testAddCountryUnBan()
    {
        $bot = new BotLicker();
        $result = $bot->unbanCountry('DE');


        $this->assertTrue($result->action_status);
    }


}
