<?php

namespace Turbo124\BotLicker\Tests;

use Illuminate\Support\Facades\Http;
use Turbo124\BotLicker\BotLicker;
use Turbo124\BotLicker\Facades\Firewall;

class CloudflareTest extends BotLickerTestCase
{

    protected array $ruleset_response = ['result' => [
            [
            "id" => "ruleset-id", 
            "name" => "Example Ruleset", 
            "description" => "Description of Example Ruleset", 
            "kind" => "custom", 
            "version" => "2", 
            "phase" => "http_request_firewall_custom", 
            "rules" => [
                    [
                        "id" => "rule-id", 
                        "version" => "2", 
                        "action" => "block", 
                        "expression" => "cf.zone.name eq example.com", 
                        "last_updated" => "2020-07-20T10:44:29.124515Z" 
                    ] 
                ], 
            "last_updated" => "2020-07-20T10:44:29.124515Z" 
            ]
        ]
    ]; 
 
    protected array $ruleset = 
        ['result' => [
            "id" => "ruleset-id",
            "name" => "Example Ruleset",
            "description" => "Description of Example Ruleset",
            "kind" => "custom",
            "version" => "2",
            "phase" => "http_request_firewall_custom",
            "rules" => [
                    [
                        "id" => "rule-id",
                        "version" => "2",
                        "action" => "block",
                        "expression" => "cf.zone.name eq example.com",
                        "last_updated" => "2020-07-20T10:44:29.124515Z"
                    ]
                ],
            "last_updated" => "2020-07-20T10:44:29.124515Z"
            ]
    ];

 
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testGetRules()
    {

        Http::fake([
            'api.cloudflare.com/client/v4/*' => Http::sequence()
                                    ->push($this->ruleset_response, 200)
                                    ->push($this->ruleset, 200),
        ]);

        $rules = Firewall::getRules();

        $this->assertIsArray($rules);

    }

    public function testBanIp()
    {

        Http::fake([
            'api.cloudflare.com/client/v4/*' => Http::sequence()
                                    ->push($this->ruleset_response, 200)
                                    ->push($this->ruleset, 200)
                                    ->push($this->ruleset, 200),
        ]);

        $result = Firewall::ban('192.168.0.7');

        $this->assertInstanceOf(BotLicker::class, $result);
    }




    public function testShowWafRules(): void
    {


        Http::fake([
            'api.cloudflare.com/client/v4/*' => Http::sequence()
                                    ->push($this->ruleset_response, 200)
                                    ->push($this->ruleset, 200),
        ]);


        $this->artisan('firewall:cf-rules')
            ->expectsTable(['Block', 'Challenge'],[]);
    }

}

