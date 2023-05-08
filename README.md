
<p align="center">
    <img src="botlicker.png">
</p>

# BotLicker

## Ban / Challenge IPs/Countries at the network edge.

**The Problem**: After researching some laravel packages that provide firewalls, a common theme was that they all block at the application level.

This may work for sites with low levels of traffic, but when dealing with a large number of requests it becomes ineffective.

Enter the BotLicker where you can send your WAF rules direct to Cloudflare for instant implementation.

### Installation

```
composer require turbo124/bot-licker
```

### Configuration

In your .env file, enter in the following environment variables:

```
CLOUDFLARE_API_KEY="your_cloudflare_API_KEY_that_has_permission to read/write/edit WAF rules"
CLOUDFLARE_EMAIL="your_cloudflare_email_address"
CLOUDFLARE_ZONE_ID="your_zone_id"
```

### Usage

To permanently ban an IP address from reaching your server.

```php
Firewall::ban('101.1.1.254');
```

To unban and IP address

```php
Firewall::unban('101.1.1.254');
```

If you would prefer to issue a challenge to an IP address

```php
Firewall::challenge('101.1.1.254');
```

To then disable

```php
Firewall::unchallenge('101.1.1.254');
```

If you would prefer to ban an entire country simply pass in the iso_3166_2 country code

```php
Firewall::banCountry('DE');
```

To then disable

```php
Firewall::unbanCountry('DE');
```

### Extending BotLicker with new providers

Currently only Cloudflare is supported, however you can easily implement the ProviderContract and generate the corresponding methods for other WAFs. Once you have created your provider, simply inject it into the `setProvider()`

```php
Firewall::setProvider(OtherWAF::class)->ban('101.1.1.254');
```