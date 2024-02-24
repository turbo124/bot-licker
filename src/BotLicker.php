<?php

namespace Turbo124\BotLicker;

use Illuminate\Support\Carbon;
use Turbo124\BotLicker\Models\BotlickerBan;
use Turbo124\BotLicker\Providers\ProviderContract;
use Turbo124\BotLicker\Exceptions\DisabledException;

class BotLicker
{    
    /** @var ProviderContract $provider */
    protected ProviderContract $provider;
    
    /**
     * IP Address
     *
     * @var string
     */
    protected ?string $ip = null;
        
    /** 
     * ISO 3166 2 character country code
     *  
     * @var string $iso_3166_2 
     */
    protected ?string $iso_3166_2 = null;

    
    /** 
     * The action to perform on this IP / Country
     * 
     * @var mixed $action 
     * */
    protected string $action = '';
    
    public bool $action_status = false;

    /**
     * Ban a IP address 
     *
     * @param  string $ip
     * @param  ?Carbon $expiry
     * @return self
     */
    public function ban(string $ip, ?\Illuminate\Support\Carbon $expiry = null)
    {
        $this->ip = $ip;

        $this->action = 'ban';

        try{

            $this->action_status = $this->preFlight()->getProvider()->banIp($ip);    

            $this->expires($expiry);

            return $this;

        }
        catch(\Exception $e) {
            return $e;
        }


    }

    /**
     * Unban a IP address 
     *
     * @param  string $ip
     * @return self
     */
    public function unban(string $ip): self
    {
    
        $this->ip = $ip;

        try {

            $this->action_status = $this->preFlight()->getProvider()->unbanIp($ip);
    
            return $this;

        }
        catch(\Exception $e) {

            throw new \Exception("Could not unban IP address {$ip} " . $e->getMessage(), 400);
        }

    }
    
    /**
     * Challenge a IP address 
     *
     * @param  string $ip
     * @param  ?Carbon $expiry
     * @return self
     */
    public function challenge(string $ip, ?\Illuminate\Support\Carbon $expiry = null): self
    {
        
        $this->ip = $ip;
        $this->action = 'challenge';

        try {
        
            $this->action_status = $this->preFlight()->getProvider()->challengeIp($ip);
            
            $this->expires($expiry);

            return $this;

        }
        catch(\Exception $e) {

        }

    }
    
    /**
     * UnChallenge a IP address 
     *
     * @param  string $ip
     * @return self
     */
    public function unchallenge(string $ip): self
    {
        
        $this->ip = $ip;

        try {
        
            $this->action_status = $this->preFlight()->getProvider()->unchallengeIp($ip);
        
            return $this;

        }
        catch(\Exception $e) {

        }


    }
    
    /**
     * Ban Country 
     *
     * @param  string $iso_3166_2
     * @param  ?Carbon $expiry
     * 
     * @return self
     */
    public function banCountry(string $iso_3166_2, ?\Illuminate\Support\Carbon $expiry = null): self
    {

        $this->iso_3166_2 = $iso_3166_2;

        $this->action = 'ban';

        try {
            
            $this->action_status = $this->preFlight()->getProvider()->banCountry($iso_3166_2);
    
            $this->expires($expiry);

            return $this;

        }
        catch(\Exception $e) {

            throw new \Exception("Could not unban {$iso_3166_2}");

        }

    }

    /**
     * UnBan Country 
     *
     * @param  string $iso_3166_2
     * 
     * @return self
     */
    public function unbanCountry(string $iso_3166_2): self
    {

        $this->iso_3166_2 = $iso_3166_2;

        try {

            $this->action_status = $this->preFlight()->getProvider()->unbanCountry($iso_3166_2);
    
            return $this;

        }
        catch(\Exception $e) {

            throw new \Exception("Could not unban {$iso_3166_2}");

        }

    }
    
    /**
     * Get the existing rules that are in the WAF
     *
     * @return array
     */
    public function getRules(): array
    {
        try {

            return $this->getProvider()->getRules();

        }
        catch(\Exception $e) {

            throw new \Exception("Could not get list of rules from the WAF");
        }

    }

    /**
     * Set Provider
     *
     * @param  ProviderContract $provider
     * @return self
     */
    public function setProvider($provider): self
    {

        $this->provider = $provider;

        return $this;

    }
        
    /**
     * expires
     *
     * @param  \Illuminate\Support\Carbon $expiry
     * @return self
     */
    public function expires(?\Illuminate\Support\Carbon $expiry = null): self
    {
        
        BotlickerBan::on(config('bot-licker.db_connection'))
        ->insert([
            'ip' => $this->ip,
            'iso_3166_2' => $this->iso_3166_2,
            'action' => $this->action,
            'expiry' => $expiry
        ]);

        return $this;
    }

    /**
     * Get Provider
     *
     * @return ProviderContract
     */
    private function getProvider(): ProviderContract
    {

        $default_provider = config('bot-licker.provider');

        return $this->provider ?? new $default_provider;

    }
    
    /**
     * Pre Flight Checks
     *
     * @return self
     */
    private function preFlight(): self
    {
        if(!config('bot-licker.enabled'))
            throw new DisabledException('Disabled package', 444);

        if($this->ip && in_array($this->ip, config('bot-licker.whitelist_ips')))
            throw new \Exception('Protected IP address, cannot be actioned', 400);

            
        if ($this->ip && in_array($this->ip, config('bot-licker.whitelist_countries'))) 
            throw new \Exception('Protected Country Code, cannot be actioned', 400);

        return $this;
    }

}