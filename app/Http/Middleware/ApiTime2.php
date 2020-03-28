<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Smart2\CommandModel\Eloquent\ApiLog;

class ApiTime
{
    public function handle($request, Closure $next)
    {
        $n = $next($request);

        $exec_time = microtime(true) - LARAVEL_START;

        ApiLog::create([
            'api' => $request->path(),
            'parameter' => json_encode($request->all(), JSON_UNESCAPED_SLASHES),
            'member_id' => $request->user()->id,
            'exec_time' => $exec_time,
            'date' => Carbon::now(),
        ]);

        return $n;
    }
}
