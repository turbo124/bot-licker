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
        
    /** @var string $ban_ip_expression */
    private string $ban_ip_expression = '(ip.src eq :ip)';
    
    /**
     * Ban Ip
     *
     * @param  string $ip
     * @return void
     */
    public function banIp(string $ip, array $params = [])
    { 
        $expression = str_replace(":ip", $ip, $this->ban_ip_expression);

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
       $filter_id = $this->getFilterbyExpression($this->ban_ip_expression.$ip);
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
        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/filters";

        $response =

        Http::withHeaders($this->getHeaders())->post($cloudflare_endpoint,[
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

    private function getFilterById($filter_id): string
    {

        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/filters/{$filter_id}";

        $response = 
        
        Http::withHeaders($this->getHeaders())->get($cloudflare_endpoint);

        // {
        //   "result": {
        //     "id": "<FILTER_ID>",
        //     "paused": false,
        //     "description": "Login from office",
        //     "expression": "ip.src eq 93.184.216.0 and (http.request.uri.path ~ \"^.*/wp-login.php$\" or http.request.uri.path ~ \"^.*/xmlrpc.php$\")"
        //   },
        //   "success": true,
        //   "errors": [],
        //   "messages": []
        // }

        if($response->successful()){
            return $response->collect()->result->id;
        }

    }

    private function getFilterbyExpression($expression): ?string
    {

        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/filters";

        $response = $this->listPaginator($cloudflare_endpoint);

        // {
        //   "result": {
                //     "id": "<FILTER_ID>",
                //     "paused": false,
                //     "description": "Login from office",
                //     "expression": "ip.src eq 93.184.216.0 and (http.request.uri.path ~ \"^.*/wp-login.php$\" or http.request.uri.path ~ \"^.*/xmlrpc.php$\")"
        //   },
        //   "success": true,
        //   "errors": [],
        //   "messages": []
        // }


        if ($response->successful()) {
            
            //Iterate through collection and search for expression;
            $paginator = $response->collect()->result_info;

            for($page = 1; $page <= $paginator->total_pages; $page++){

                $response = $this->listPaginator($cloudflare_endpoint, $page);

                foreach($response->collect()->result as $filter){

                    if($filter->expression == $expression){
                        return $filter->id;
                    }

                }

            }
            
        }

        return null;
    }

    private function listPaginator($url, $page = 1, $per_page = 50)
    {
        return

        Http::withHeaders($this->getHeaders())->get($url, [
            'page' => $page,
            'per_page' => $per_page
        ]);
    }

    private function hydratePagination($response)
    {

        $pagination = $response->collect()->result_info;

        $pagination = [
            'total_pages' => $pagination->total_pages,
            'count' => $pagination->count,
            'per_page' => $pagination->per_page,
            'page' => $pagination->page,
            'total_count' => $pagination->total_count,
        ];

        return $pagination;

    }

}