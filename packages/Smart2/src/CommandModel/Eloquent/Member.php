<?php

namespace Smart2\CommandModel\Eloquent;

use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Smart2\CommandModel\Eloquent\Member.
 *
 * @property int $id
 * @property null|int $sponsor_id
 * @property string $family_name
 * @property string $given_name
 * @property string $email
 * @property null|string $password_digest
 * @property null|\Illuminate\Support\Carbon $created_at
 * @property null|\Illuminate\Support\Carbon $updated_at
 * @property null|string $remember_token
 * @property int $login_control_flag
 * @property string $started_at
 * @property null|string $ended_at
 * @property int $init_login_flag
 * @property-read \Illuminate\Notifications\DatabaseNotification[]|\Illuminate\Notifications\DatabaseNotificationCollection $notifications
 * @property-read null|int $notifications_count
 * @property-read null|\Smart2\CommandModel\Eloquent\Sponsor $sponsor
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member query()
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereFamilyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereGivenName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereInitLoginFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereLoginControlFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member wherePasswordDigest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereSponsorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\Smart2\CommandModel\Eloquent\Member whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Member extends Authenticatable
{
    use Notifiable;

    /**
     * table name.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password_digest',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password_digest', 'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->attributes['password_digest'];
    }

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }

    // TODO - kinoshita: あとでtraitに
    public function hasPermission(string $key): bool
    {
        $prefix = 'permission.list.';
        $configKey = $prefix . $key;

        if (!\Config::has($configKey)) {
            // TODO - kinoshita: あとでclassを作る。
            throw new \Exception('unknown permission');
        }

        if (!array_key_exists($key, $this->sponsor->sponsorRole->permissions)) {
            return false;
        }

        $permission = $this->sponsor->sponsorRole->permissions[$key];

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
        if ($this->sponsor->status != 'enabled') {
            return false;
        }

        if ($this->sponsor->started_at === null) {
            return false;
        }

        if ($this->sponsor->ended_at === null) {
            return true;
        }

        $now = new Carbon();
        $startedAt = new Carbon($this->sponsor->started_at . ' 00:00:00');
        $endedAt = new Carbon($this->sponsor->ended_at . ' 23:59:59');

        return $now->between($startedAt, $endedAt);
    }

    public function isDuringTrial(string $from, string $to): bool
    {
        $trialSettings = $this->sponsor->sponsorTrial->settings;

        if ($trialSettings === null || $trialSettings['search_range'] === null || $trialSettings['search_range']['start'] === null || $trialSettings['search_range']['end'] === null) {
            // レコードがない場合は、期間チェックをtrueで返却する
            return true;
        }

        $now = new \Carbon\Carbon();
        $startedAt = new \Carbon\Carbon($trialSettings['search_range']['start']);
        $endedAt = new \Carbon\Carbon($trialSettings['search_range']['end']);

        $cFrom = new \Carbon\Carbon($from);
        $cTo = new \Carbon\Carbon($to);

        return $startedAt->lessThanOrEqualTo($cFrom->startOfDay()) && $endedAt->greaterThanOrEqualTo($cTo->startOfDay());
    }

    /**
     * パスワードリセット通知の送信
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
