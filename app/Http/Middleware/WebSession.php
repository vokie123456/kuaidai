<?php

namespace App\Http\Middleware;

use Closure;
use Config;

class WebSession
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @param $lifetime
     * @return mixed
     */
    public function handle($request, Closure $next, $lifetime)
    {
        // 设置Session过期时间
        Config::set('session.lifetime', $lifetime);

        return $next($request);
    }

}