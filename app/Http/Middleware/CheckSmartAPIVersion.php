<?php

namespace App\Http\Middleware;

use Closure;

class CheckSmartAPIVersion
{
    public function handle($request, Closure $next)
    {
        $expected = config('const.SMART_API_VERSION');
        $actual = $request->cookie('Smart-API-Version');
        $cookie = cookie()->forever('Smart-API-Version', $expected);

        if (strpos($request->url(), 'http://localhost') !== 0 && $actual !== null && $actual !== $expected) {
            $cookie = cookie('Smart-API-Version', $expected);
            abort(426, 'smart_api_version_error', ['Set-Cookie' => $cookie->__toString()]);
        }

        $response = $next($request);

        if (method_exists($response, 'withCookie')) {
            return $response->withCookie($cookie);
        }

        return $response;
    }
}
