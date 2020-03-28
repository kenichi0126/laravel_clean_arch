<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Session;
use Smart2\CommandModel\Eloquent\MemberAccess;
use Smart2\CommandModel\Eloquent\SametimeLoginLog;

class CheckSametimeLogin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::getUser();

        if ($this->isNeedToCheck($user->login_control_flag)) {
            $memberAccess = MemberAccess::query()->find($user->id);

            if (!$this->isValidToken($memberAccess->login_token)) {
                SametimeLoginLog::create([
                    'member_id' => $user->id,
                    'created_at' => Carbon::now(),
                ]);
                abort(401, 'sametime_login_error');
            }
        }
        return $next($request);
    }

    private function isNeedToCheck(int $loginControlFlag): bool
    {
        return $loginControlFlag === 1;
    }

    private function isValidToken(string $token): bool
    {
        return Session::get('login_token') === $token;
    }
}
