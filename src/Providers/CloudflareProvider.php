<?php

namespace Turbo124\BotLicker\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 *
 * Create firewall rules	POST zones/<ZONE_ID>/firewall/rules	Handled as a single transaction. If there is an error, the entire operation fails.
 * List firewall rules	    GET zones/<ZONE_ID>/firewall/rules	Lists all current firewall rules. Results return paginated with 25 items per page by default. Use optional parameters to narrow results.
 * Get a firewall rule	    GET zones/<ZONE_ID>/firewall/rules/<RULE_ID>	Retrieve a single firewall rule by ID.
 * Update a firewall rule	PUT zones/<ZONE_ID>/firewall/rules/<RULE_ID>	Update a single firewall rule by ID.
 * Delete firewall rules	DELETE zones/<ZONE_ID>/firewall/rules
 * Delete a firewall rule by ID.
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
    
    /** @var string $ban_country_expression */
    private string $ban_country_expression = '(ip.geoip.country eq ":iso_3166_2")';

    /** @var string $ban_filter_ref */
    private string $ban_filter_ref = 'bot-licker.ban';

    /** @var string $challenge_filter_ref */
    private string $challenge_filter_ref = 'bot-licker.challenge';

    /**
     * Ban Ip
     *
     * @param  string $ip
     * @param  array $params
     * 
     * @return bool
     */
    public function banIp(string $ip, array $params = []): bool
    {

        $expression = str_replace(":ip", $ip, $this->ban_ip_expression);

        $filter = $this->getFilter($expression, $this->ban_filter_ref);

        return $this->addResourceToFilter($filter, $expression);

    }

    /**
     * Unban Ip
     *
     * @param  string $ip
     * @param  array $params
     * 
     * @return bool
     */
    public function unbanIp(string $ip, array $params = []): bool
    {

        $expression = str_replace(":ip", $ip, $this->ban_ip_expression);

        $filter = $this->getFilter($expression, $this->ban_filter_ref);

        return $this->removeResourceFromFilter($filter, $expression);

    }

    /**
     * Challenge Ip
     *
     * @param  string $ip
     * @param  array $params
     * 
     * @return bool
     */
    public function challengeIp(string $ip, array $params = []): bool
    {

        $expression = str_replace(":ip", $ip, $this->ban_ip_expression);

        $filter = $this->getFilter($expression, $this->challenge_filter_ref);

        return $this->addResourceToFilter($filter, $expression);

    }

    /**
     * Unchallenge Ip
     *
     * @param  string $ip
     * @param  array $params
     * 
     * @return bool
     */
    public function unchallengeIp(string $ip, array $params = []): bool
    {
        
        $expression = str_replace(":ip", $ip, $this->ban_ip_expression);

        $filter = $this->getFilter($expression, $this->challenge_filter_ref);

        return $this->removeResourceFromFilter($filter, $expression);

    }

    /**
     * Get Ip Info
     *
     * @param  string $ip
     * @param  array $params
     * 
     * @return void
     */
    public function getIpInfo(string $ip, array $params = [])
    {

    }

    /**
     * Get Ip List
     *
     * @param  string $zone
     * @param  array $params
     * 
     * @return void
     */
    public function getIpList(string $zone, array $params = [])
    {

    }

    /**
     * Get Ip Count
     *
     * @param  string $zone
     * @param  array $params
     * 
     * @return void
     */
    public function getIpCount(string $zone, array $params = [])
    {

    }

    /**
     * Ban Country
     *
     * @param  string $iso_3166_2
     * @param  array $params
     * 
     * @return void
     */
    public function banCountry(string $iso_3166_2, array $params = []): bool
    {

        $expression = str_replace(":iso_3166_2", $iso_3166_2, $this->ban_country_expression);

        $filter = $this->getFilter($expression, $this->ban_filter_ref);

        return $this->addResourceToFilter($filter, $expression);


    }

    /**
     * Unban Country
     *
     * @param  string $iso_3166_2
     * @param  array $params
     * 
     * @return void
     */
    public function unbanCountry(string $iso_3166_2, array $params = []): bool
    {

        $expression = str_replace(":iso_3166_2", $iso_3166_2, $this->ban_country_expression);

        $filter = $this->getFilter($expression, $this->ban_filter_ref);

        return $this->removeResourceFromFilter($filter, $expression);


    }

    /**
     * SetZone
     *
     * @param  string $zone
     * 
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
            'X-Auth-Email' => config('bot-licker.cloudflare.email'),
            'Content-Type' => 'application/json',
            // 'Authorization: Bearer' => config('bot-licker.cloudflare.api_key'),
        ];
    }

    /**
     * Adds a IP to a filter
     *
     * To ensure we don't insert duplicates we filter the array first,
     * and then push the new expression into the collection
     *
     * @param  mixed $filter
     * @param  mixed $expression
     *
     * @return bool
     */
    private function addResourceToFilter($filter, $expression): bool
    {
        $updated_expression = collect(explode("or", $filter['expression']))->filter(function ($current) use ($expression) {
            return $current != $expression;
        })->push($expression)->implode("or");

        return $this->updateFilter($filter, $updated_expression);
    }

    /**
     * Removes a ip from a filter
     *
     * @param  mixed $filter
     * @param  mixed $removable_expression
     *
     * @return bool
     */
    private function removeResourceFromFilter($filter, $removable_expression): bool
    {

        $updated_expression = collect(explode("or", $filter['expression']))->filter(function ($expression) use ($removable_expression) {
            return $expression != $removable_expression;
        })->implode("or");

        return $this->updateFilter($filter, $updated_expression);

    }
    
    /**
     * Get all firewall rules
     *
     * @return Illuminate\Support\Collection
     */
    public function getRules(): \Illuminate\Support\Collection
    {

        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/filters";

        $response = $this->listPaginator($cloudflare_endpoint);

        if($response->successful())
            return $response->collect()['result'];

        throw new \Exception("Could not get rules " . $response->body());
    }
    /**
     * Get or Create a new filter
     *
     * Uses a iterator to search for the designated ref and
     * return the filter or creates a new one.
     *
     * @param  string $expression
     * @param  string $filter_ref
     *
     * @return array
     */
    private function getFilter(string $expression, string $filter_ref): array
    {

        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/filters";

        $response = $this->listPaginator($cloudflare_endpoint);

        if ($response->successful()) {

            $paginator = $response->collect()['result_info'];

            for($page = 1; $page <= $paginator['total_pages']; $page++) {

                $response = $this->listPaginator($cloudflare_endpoint, $page);

                foreach($response->collect()['result'] as $filter) {

                    if($filter['ref'] == $filter_ref) {
                        return $filter;
                    }

                }

            }

        }

        return $this->createFilter($expression, $filter_ref);

    }

    /**
     * Creates a new firewall rule in Cloudflare
     *
     * @param  string $filter_id
     * @param  string $ref The reference key we use for this rule
     *
     * @return void
     */
    private function createRule(string $filter_id, string $ref)
    {
        match($ref) {
            'bot-licker.ban' => $action = 'block',
            'bot-licker.challenge' => $action = 'managed_challenge',
            default => $action = 'block',
        };

        $response =

        Http::withHeaders($this->getHeaders())->post("{$this->url}zones/{$this->getZone()}/firewall/rules", [
            [
            "filter" => [
                "id"=> $filter_id
            ],
            "action" => $action,
            "description" => "Bot Licker rule {$action} on " . Carbon::now()->format('Y-m-d h:i:s'),
            "ref" => $ref
            ]
        ]);

        if($response->failed()) {
            throw new \Exception("Could not create rule for {$ref} "  . $response->body());
        }

    }

    /**
     * Creates a new Firewall Filter
     *
     * @param  string $expression
     * @param  string $ref
     *
     * @return \Illuminate\Support\Collection
     */
    private function createFilter(string $expression, string $ref): \Illuminate\Support\Collection
    {
        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/filters";

        $response =
        Http::withHeaders($this->getHeaders())->post($cloudflare_endpoint, [
            [
            "expression"=> $expression,
            "description" => "Bot Licker filter {$ref} created on " . Carbon::now()->format('Y-m-d h:i:s'),
            "ref" => $ref
            ],
        ]);

        if($response->successful()) {
            //create the rule and associate the filter ID
            $this->createRule($response->collect()["result"][0]["id"], $ref);

            return $response->collect();

        } else {
            throw new \Exception("Could not create filter for {$expression} "  . $response->body());
        }

    }

    /**
     * Updates a given filter with a new expression
     *
     * @param  array $filter
     * @param  string $expression
     *
     * @return bool
     */
    private function updateFilter(array $filter, string $expression): bool
    {

        $response =

        Http::withHeaders($this->getHeaders())->put("{$this->url}zones/{$this->getZone()}/filters/{$filter['id']}", [

            "expression" => $expression,
            "description" => $filter['description'],
            "ref" => $filter['ref'],

        ]);

        if($response->successful()) {
            return true;
        } else {
            throw new \Exception($response->body() ." ". $response->body(), $response->status());
        }

    }

    /**
     * Returns a list response for pagination
     *
     * @param  string $url
     * @param  int $page
     * @param  int $per_page
     *
     * @return Response
     */
    private function listPaginator($url, $page = 1, $per_page = 50): Response
    {
        return

        Http::withHeaders($this->getHeaders())->get($url, [
            'page' => $page,
            'per_page' => $per_page
        ]);
    }

}
