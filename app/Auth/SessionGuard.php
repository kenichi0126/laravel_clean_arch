<?php

namespace App\Auth;

use Carbon\Carbon;
use Illuminate\Auth\SessionGuard as BaseSessionGuard;

class SessionGuard extends BaseSessionGuard
{
    public function hasPermission(string $key): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        if (!array_key_exists($key, $user->sponsor->sponsorRole->permissions)) {
            return false;
        }

        $permission = $user->sponsor->sponsorRole->permissions[$key];

        if (!array_key_exists('contract', $permission)) {
            return true;
        }

        $now = new Carbon();
        $startedAt = new Carbon($permission['contract']['start']);
        $endedAt = new Carbon($permission['contract']['end']);

        return $now->between($startedAt, $endedAt);
    }

    public function isValidContract(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        if ($user->sponsor->status != 'enabled') {
            return false;
        }

        if ($user->sponsor->started_at === null) {
            return false;
        }

        if ($user->sponsor->ended_at === null) {
            return true;
        }

        $now = new Carbon();
        $startedAt = new Carbon($user->sponsor->started_at . ' 00:00:00');
        $endedAt = new Carbon($user->sponsor->ended_at . ' 23:59:59');

        return $now->between($startedAt, $endedAt);
    }

    public function isDuringTrial(string $from, string $to): bool
    {
        $user = $this->user();

        if ($user === null) {
            return false;
        }

        $trialSettings = $user->sponsor->sponsorTrial->settings;

        if ($trialSettings === null || $trialSettings['search_range'] === null || $trialSettings['search_range']['start'] === null || $trialSettings['search_range']['end'] === null) {
            // レコードがない場合は、期間チェックをtrueで返却する
            return true;
        }

        $startedAt = new Carbon($trialSettings['search_range']['start']);
        $endedAt = new Carbon($trialSettings['search_range']['end']);

        $cFrom = new Carbon($from);
        $cTo = new Carbon($to);

        return $startedAt->lessThanOrEqualTo($cFrom->startOfDay()) && $endedAt->greaterThanOrEqualTo($cTo->startOfDay());
    }
}
