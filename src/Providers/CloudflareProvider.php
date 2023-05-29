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

    /** @var string $account */
    private ?string $account = null;

    /** @var string $ban_ip_expression */
    private string $ban_ip_expression = '(ip.src eq :ip)';

    /** @var string $ban_country_expression */
    private string $ban_country_expression = '(ip.geoip.country eq ":iso_3166_2")';

    /** @var string $ruleset_name */
    private string $ruleset_name = 'http_request_firewall_custom';

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

        return $this->addRule($expression, 'block');
        
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

        return $this->removeRule($expression, 'block');


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

        return $this->addRule($expression, 'managed_challenge');

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

        return $this->removeRule($expression, 'managed_challenge');

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

        return $this->addRule($expression, 'block');

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

        return $this->removeRule($expression, 'block');


    }

    public function removeRule($expression, $action)
    {

        $ruleset = $this->getRuleset();
        $rule = false;

        if(isset($ruleset['rules'])) {
            $rule = collect($ruleset['rules'])->first(function ($rules) use ($action) {
                return $rules['action'] == $action;
            });
        }

        if($rule) {
            return $this->updateRuleExpression($ruleset, $this->removeExpression($rule, $expression), $rule);
        }

    }

    public function addRule($expression, $action)
    {
        $ruleset = $this->getRuleset();
        $rule = false;
        
        if(isset($ruleset['rules'])) {
            $rule = collect($ruleset['rules'])->first(function ($rules) use ($action) {
                return $rules['action'] == $action;
            });
        }

        echo print_r($rule, true);

        if($rule) {
            return $this->updateRuleExpression($ruleset, $this->addExpression($rule, $expression), $rule);
        }

        return $this->addRuleParent($ruleset, $expression, $action);
    }


    public function addRuleParent(array $ruleset, string $expression, string $action)
    {

        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/rulesets/{$ruleset['id']}/rules";

        $rule = [
            'action' => $action,
            'expression' => $expression,
            'description' => "Added by botlicker on " . now()->toDateTimeString()
        ];

        $response =
        Http::withHeaders($this->getHeaders())->post($cloudflare_endpoint, $rule);

        if($response->successful()) {
            return true;
        }

        throw new \Exception("Could not add rule {$action} => " . $response->body());

    }

    public function updateRuleExpression(array $ruleset, string $expression, array $rule)
    {
        $rule['expression'] = $expression;

        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/rulesets/{$ruleset['id']}/rules/{$rule['id']}";

        $response =
        Http::withHeaders($this->getHeaders())->patch($cloudflare_endpoint, $rule);

        echo print_r($response->body(), true);  

        if($response->successful()) {
            return true;
        }

        throw new \Exception("Could not get rules " . $response->body());

    }

    public function addExpression($rule, $expression): string
    {
        
        return collect(explode("or", $rule['expression']))->filter(function ($current) use ($expression) {
            return $current != $expression;
        })->push($expression)->implode("or");

    }

    public function removeExpression($rule, $expression)
    {
        
        return collect(explode("or", $rule['expression']))->filter(function ($current) use ($expression) {
            return $current != $expression;
        })->implode("or");

    }

    public function getRuleset()
    {
        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/rulesets";

        $response = $this->listPaginator($cloudflare_endpoint);
    
        if($response->successful()) {
            
            $result = $response->collect()['result'];

            $ruleset = collect($result)->where('phase', $this->ruleset_name)->first();

            $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/rulesets/{$ruleset['id']}";

            return $this->listPaginator($cloudflare_endpoint)->collect()['result'];
            
        }

        throw new \Exception("Could not get rules " . $response->body());

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
     * getAccount
     *
     * @return string
     */
    private function getAccount(): string
    {
        return $this->account ?? config('bot-licker.cloudflare.account_id');
    }

    /**
     * Set Account
     *
     * @param  string $account
     *
     * @return self
     */
    public function setAccount(string $account): self
    {

        $this->account = $account;

        return $this;

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
