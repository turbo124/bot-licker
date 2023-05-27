<?php


namespace Turbo124\BotLicker\Http\Middleware;

use Closure;
use Turbo124\BotLicker\Models\BotlickerLog;
use Illuminate\Contracts\Routing\TerminableMiddleware;

class QueryLog implements TerminableMiddleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        // Store the session data...
        BotlickerLog::on(config('bot-licker.db_connection'))->insert([
            'ip' => $request->ip(),
            'uri' => substr(urldecode($request->getRequestUri()), 0, 180),
        ]);

    }

}
