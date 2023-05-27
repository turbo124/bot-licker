<?php 

return [
    
    /**
     * Enable or disable the firewall
     */
    'enabled' => env('FIREWALL_ENABLED', true),
    
    /**
     * The firewall provider to use
     */
    'provider' => \Turbo124\BotLicker\Providers\CloudflareProvider::class,

    /**
     * Specific provider configuration
     */
    'cloudflare' => [
        'api_key' => env('CLOUDFLARE_API_KEY'),
        'email'   => env('CLOUDFLARE_EMAIL'),
        'zone_id' => env('CLOUDFLARE_ZONE_ID'),
    ],

    /**
     * Array of IPs to never ban
     * 
     * @var array<string>
     */
    'whitelist_ips' => [
        '127.0.0.1',
    ],

    /**
     * Array of Countries to never ban - ISO 3166 2 character country code
     */
    'whitelist_countries' => [],

    /**
     * Analyze inbound requests and match against rules
     */
    'query_log' => false,

    /**
     * Preference a particular DB connection for this service
     */
    'db_connection' => 'default',

    'events' => [

    ],
];