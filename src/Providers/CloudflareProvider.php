<?php

namespace Turbo124\BotLicker\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

/**
 * 
// Create firewall rules	POST zones/<ZONE_ID>/firewall/rules	Handled as a single transaction. If there is an error, the entire operation fails.
// List firewall rules	    GET zones/<ZONE_ID>/firewall/rules	Lists all current firewall rules. Results return paginated with 25 items per page by default. Use optional parameters to narrow results.
// Get a firewall rule	    GET zones/<ZONE_ID>/firewall/rules/<RULE_ID>	Retrieve a single firewall rule by ID.
// Update a firewall rule	PUT zones/<ZONE_ID>/firewall/rules/<RULE_ID>	Update a single firewall rule by ID.
// Delete firewall rules	DELETE zones/<ZONE_ID>/firewall/rules	
// Delete a firewall rule by ID.
 * 
 */
class CloudflareProvider implements ProviderContract
{    
    /** @var string $url */
    private string $url = 'https://api.cloudflare.com/client/v4/';
    
    /** @var string $zone */
    private ?string $zone = null;
    
    /**
     * Ban Ip
     *
     * @param  string $ip
     * @return void
     */
    public function banIp(string $ip, array $params = [])
    { 
        $expression = 'ip.src eq ' . $ip;

        $filter_id = $this->createFilter($expression);

        $response = 

        Http::post("{$this->url}zones/{$this->getZone()}/firewall/rules",[
            "filter" => [
                "id"=> $filter_id
            ],
            "action" => "block",
            "description" => "Bot Licker rule on " . Carbon::now()->format('Y-m-d h:i:s')
        ]);

        if($response->successful()){
            
        }
    }
    
    /**
     * Unban Ip
     *
     * @param  string $ip
     * @return void
     */
    public function unbanIp(string $ip, array $params = []) 
    { 

    }
    
    /**
     * Challenge Ip
     *
     * @param  string $ip
     * @return void
     */
    public function challengeIp(string $ip, array $params = []) 
    { 

    }
    
    /**
     * Unchallenge Ip
     *
     * @param  string $ip
     * @return void
     */
    public function unchallengeIp(string $ip, array $params = []) 
    { 

    }
    
    /**
     * Get Ip Info
     *
     * @param  string $ip
     * @return void
     */
    public function getIpInfo(string $ip, array $params = []) 
    { 

    }
    
    /**
     * Get Ip List
     *
     * @param  string $zone
     * @return void
     */
    public function getIpList(string $zone, array $params = []) 
    { 

    }
    
    /**
     * Get Ip Count
     *
     * @param  string $zone
     * @return void
     */
    public function getIpCount(string $zone, array $params = []) 
    { 

    }
    
    /**
     * Ban Country
     *
     * @param  string $iso_3166_2
     * @return void
     */
    public function banCountry(string $iso_3166_2, array $params = []) 
    { 

    }
    
    /**
     * Unban Country
     *
     * @param  string $iso_3166_2
     * @return void
     */
    public function unbanCountry(string $iso_3166_2, array $params = []) 
    { 

    }
    
    /**
     * SetZone
     *
     * @param  string $zone
     * @return self
     */
    public function setZone(string $zone): self 
    { 

        $this->zone = $zone;

        return $this;
    }
    
    /**
     * Get the configured Zone
     *
     * @return string
     */
    private function getZone(): string
    {
        return $this->zone ?? config('bot-licker.cloudflare.zone_id');
    }
    
    /**
     * Get Auth headersHeaders
     *
     * @return array
     */
    private function getHeaders(): array
    {
        return [
            'X-Auth-Key' => config('bot-licker.cloudflare.api_key'),
            'X-Auth-Email' => config('bot-licker.cloudflare.email')
        ];
    }

    /**
     * Create Filter
     *
     * @param  string $expression
     * @return void
     */
    private function createFilter($expression)
    {
        
        $response =

        Http::withHeaders($this->getHeaders())->post("{$this->url}zones/{$this->getZone()}/filters",[
            'expression' => $expression
        ]);

            // "result": [
            //     {
            //       "id": "<FILTER_ID_1>",
            //       "paused": false,
            //       "expression": "ip.src eq 93.184.216.0"
            //     },
            // ]

        if($response->successful()){
            return $response->collect();
        }

    }

}