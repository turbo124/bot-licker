<?php

namespace Turbo124\BotLicker\Providers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

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
        
    /**
     * Get the rules in the WAF
     *
     * @return array
     */
    public function getRules(): array
    {
        $rules = [
            'block' => [],
            'managed_challenge' => [],
        ];

        $ruleset = $this->getRuleset();

        if(isset($ruleset['rules'])) {
            
            $rules = collect($ruleset['rules'])->map(function ($rule){
                return [
                    $rule['action'] => explode("or",$rule['expression']),
                ];
            })->toArray();
            
        }

        return $rules;
    }

    /**
     * Removes a rule from the Firewall
     *
     * @param  string $expression
     * @param  string $action
     * 
     * @return bool
     */
    public function removeRule($expression, $action): bool
    {

        $ruleset = $this->getRuleset();
        
        $rule = false;

       if(isset($ruleset['rules']))
        {
            $rule = collect($ruleset['rules'])->first(function ($rules) use ($action) {
                return $rules['action'] == $action;
            });
        }

        if($rule) {
            return $this->updateRuleExpression($ruleset, $this->removeExpression($rule, $expression), $rule);
        }

        return true;
    }
    
    /**
     * Adds a rule from the Firewall
     *
     * @param  string $expression
     * @param  string $action
     * 
     * @return bool
     */
    public function addRule($expression, $action)
    {
        $ruleset = $this->getRuleset();

        $rule = false;
        
        if(isset($ruleset['rules'])) {
            $rule = collect($ruleset['rules'])->first(function ($rules) use ($action) {
                return $rules['action'] == $action;
            });
        }

        if($rule) {
            return $this->updateRuleExpression($ruleset, $this->addExpression($rule, $expression), $rule);
        }

        return $this->addRuleParent($ruleset, $expression, $action);
    }

    
    /**
     * Adds a new rule
     *
     * @param  array $ruleset
     * @param  string $expression
     * @param  string $action
     * 
     * @return bool
     */
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
    
    public function deleteRule($ruleset, $rule) {

        $ruleset_id = $ruleset['id'];
        $rule_id = $rule['id'];

        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/rulesets/{$ruleset_id}/rules/{$rule_id}";

        $response =

        Http::withHeaders($this->getHeaders())->delete($cloudflare_endpoint, []);

        if($response->successful()) {
            return true;
        }

        throw new \Exception("Could not get rules " . $response->body());

    }

    /**
     * Updates the rules expression
     *
     * @param  array $ruleset
     * @param  string $expression
     * @param  array $rule
     * 
     * @return bool
     */
    public function updateRuleExpression(array $ruleset, string $expression, array $rule): bool
    {
        $rule['expression'] = $expression;

        if(strlen($expression) == 0)
            return $this->deleteRule($ruleset, $rule);

        $cloudflare_endpoint = "{$this->url}zones/{$this->getZone()}/rulesets/{$ruleset['id']}/rules/{$rule['id']}";

        $response =

        Http::withHeaders($this->getHeaders())->patch($cloudflare_endpoint, $rule);

        if($response->successful()) {
            return true;
        }

        throw new \Exception("Could not get rules " . $response->body());

    }
    
    /**
     * Adds a expression to the rule
     *
     * @param  array $rule
     * @param  string $expression
     * 
     * @return string
     */
    public function addExpression($rule, $expression): string
    {
        
        return collect(explode("or", $rule['expression']))->filter(function ($current) use ($expression) {
            return $current != $expression;
        })->push($expression)->implode("or");

    }
    
    /**
     * Removes a expression to the rule
     *
     * @param  array $rule
     * @param  string $expression
     * 
     * @return string
     */
    public function removeExpression($rule, $expression)
    {
        
        return collect(explode("or", $rule['expression']))->filter(function ($current) use ($expression) {
            return $current != $expression;
        })->implode("or");

    }
    
    /**
     * Returns the custom firewall ruleset
     *
     * @return array
     */
    public function getRuleset(): array
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
