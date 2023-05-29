<?php

namespace Turbo124\BotLicker\Tests;

use Orchestra\Testbench\TestCase;
use Turbo124\BotLicker\BotLicker;
use Illuminate\Console\Application;
use Illuminate\Support\Facades\Artisan;
use Turbo124\BotLicker\Facades\Firewall;
use Turbo124\BotLicker\Commands\FirewallRules;
use Turbo124\BotLicker\BotLickerServiceProvider;
use Symfony\Component\Console\Output\BufferedOutput;
use Turbo124\BotLicker\Providers\CloudflareProvider;
use Turbo124\BotLicker\Providers\CloudflareProvider2;

class ConfigTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

    }

    protected function getPackageProviders($app)
    {
        return [
        BotLickerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup

        $filename = __DIR__."/../.env";
        if (! file_exists($filename)) {
            throw new \Exception("could not load env vars ".__DIR__);
        }

        $vars = array();
        if ($filename !== '') {
            $vars = parse_ini_file($filename);
            foreach ($vars as $varKey => $varValue) {
                $_ENV[$varKey] = $varValue;
            }
        }

        $app['config']->set('bot-licker.enabled', true);
        $app['config']->set('bot-licker.cloudflare.zone_id', $_ENV["CLOUDFLARE_ZONE_ID"]);
        $app['config']->set('bot-licker.cloudflare.account_id', $_ENV["CLOUDFLARE_ACCOUNT_ID"]);
        $app['config']->set('bot-licker.cloudflare.email', $_ENV["CLOUDFLARE_EMAIL"]);
        $app['config']->set('bot-licker.cloudflare.api_key', $_ENV["CLOUDFLARE_API_KEY"]);

    }


    public function testPrintRules()
    {


        Application::starting(function ($artisan) {
            $artisan->add(app(FirewallRules::class));
        });

        $output = new BufferedOutput();
        
        // $x = Artisan::call('firewall:rules', [], $output);
        
        // echo print_r($output->fetch(),1);
        $cp = new CloudflareProvider();
        
        echo print_r($cp->unchallengeIp('192.168.0.125'),1);

        // echo print_r($cp->getRuleset());
        


    }

    /** @test */
    // public function testValidInstanceType()
    // {
    //     $firewall = new Firewall();
    //     $this->assertTrue($firewall instanceof Firewall);
    // }

    // public function testAddIp()
    // {

    //     $bot = new BotLicker();
    //     $result = $bot->ban('101.55.31.19');

    //     $this->assertTrue($result);
    // }

    // public function testRemoveIp()
    // {
    //     $bot = new BotLicker();
    //     $result = $bot->unban('101.55.31.120');

    //     $this->assertTrue($result);
    // }

    // public function testAddChallengeIP()
    // {
    //     $bot = new BotLicker();
    //     $result = $bot->challenge('101.55.32.119');

    //     $this->assertTrue($result);
    // }

    // public function testRemoveChallengeIP()
    // {
    //     $bot = new BotLicker();
    //     $result = $bot->challenge('101.55.32.120');

    //     $this->assertTrue($result);
    // }

    // public function testAddCountryBan()
    // {
    //     $bot = new BotLicker();
    //     $result = $bot->banCountry('DE');

    //     $this->assertTrue($result);
    // }

    // public function testAddCountryUnBan()
    // {
    //     $bot = new BotLicker();
    //     $result = $bot->unbanCountry('DE');

    //     $this->assertTrue($result);
    // }


}
