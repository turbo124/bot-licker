<?php


namespace Turbo124\BotLicker\Http\Middleware;

use Closure;
use Turbo124\BotLicker\Models\BotlickerLog;

class QueryLog
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $uri = urldecode($request->getRequestUri());

            if(strlen($uri) > 3) {

                // Store the session data...
                BotlickerLog::on(config('bot-licker.db_connection'))->insert([
                    'ip' => $request->ip(),
                    'url' => substr($uri, 0, 180),
                ]);
            }
    }

}
