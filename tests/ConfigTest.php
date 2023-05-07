<?php

namespace Turbo124\BotLicker\Tests;

use Orchestra\Testbench\TestCase;
use Turbo124\BotLicker\BotLicker;
use Illuminate\Console\Application;
use Illuminate\Support\Facades\Artisan;
use Turbo124\BotLicker\Facades\Firewall;
use Turbo124\BotLicker\Commands\FirewallRules;
use Turbo124\BotLicker\BotLickerServiceProvider;

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

        $app['config']->set('bot-licker.cloudflare.zone_id', $_ENV["CLOUDFLARE_ZONE_ID"]);
        $app['config']->set('bot-licker.cloudflare.email', $_ENV["CLOUDFLARE_EMAIL"]);
        $app['config']->set('bot-licker.cloudflare.api_key', $_ENV["CLOUDFLARE_API_KEY"]);


    }


    public function testPrintRules()
    {


        Application::starting(function ($artisan) {
            $artisan->add(app(FirewallRules::class));
        });

        $x = Artisan::call('firewall:rules');
        
        echo print_r($x,1);
    }

    /** @test */
    public function testValidInstanceType()
    {
        $firewall = new Firewall();
        $this->assertTrue($firewall instanceof Firewall);
    }

    public function testAddIp()
    {

        $bot = new BotLicker();
        $result = $bot->ban('101.55.31.19');

        $this->assertTrue($result);
    }

    public function testRemoveIp()
    {
        $bot = new BotLicker();
        $result = $bot->unban('101.55.31.120');

        $this->assertTrue($result);
    }

    public function testAddChallengeIP()
    {
        $bot = new BotLicker();
        $result = $bot->challenge('101.55.32.119');

        $this->assertTrue($result);
    }

    public function testRemoveChallengeIP()
    {
        $bot = new BotLicker();
        $result = $bot->challenge('101.55.32.120');

        $this->assertTrue($result);
    }

    public function testAddCountryBan()
    {
        $bot = new BotLicker();
        $result = $bot->banCountry('DE');

        $this->assertTrue($result);
    }

    public function testAddCountryUnBan()
    {
        $bot = new BotLicker();
        $result = $bot->unbanCountry('DE');

        $this->assertTrue($result);
    }


}
