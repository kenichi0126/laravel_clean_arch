<?php

namespace Smart2\Application\Services;

use Carbon\Carbon;
use Hash;
use Illuminate\Auth\AuthenticationException;
use Smart2\Application\Services\Notices\NoticesService;
use Smart2\CommandModel\Eloquent\Member;
use Switchm\SmartApi\Queries\Dao\Rdb\SponsorDao;
use Switchm\SmartApi\Queries\Dao\Rdb\UserInfoDao;

class UserInfoService
{
    protected $userInfoDao;

    protected $sponsorDao;

    public function __construct(UserInfoDao $userInfoDao, SponsorDao $sponsorDao)
    {
        $this->userInfoDao = $userInfoDao;
        $this->sponsorDao = $sponsorDao;
    }

    public function execute(int $memberId): ?\stdClass
    {
        $user = $this->userInfoDao->getUserInfo($memberId);
        $user->aggregate_setting_code = $user->aggregate_setting_code !== null ? $user->aggregate_setting_code : json_encode([['code' => 'personal'], ['code' => 'household']]);
        $user->aggregate_setting_code = json_decode($user->aggregate_setting_code);

        $hasKantoPermission = \Auth()->user()->hasPermission('smart2::region_kanto::view');
        $hasKansaiPermission = \Auth()->user()->hasPermission('smart2::region_kansai::view');

        if ($hasKantoPermission && $hasKansaiPermission) {
            // どちらも存在する場合は、DB初期値をそのまま使用
        } elseif ($hasKansaiPermission) {
            $user->aggregate_setting_region_id = 2;
        } else {
            $user->aggregate_setting_region_id = 1;
        }

        $sponsor = $this->sponsorDao->sponsorBasic($user->sponsor_id);

        $today = Carbon::now();
        $startedAt = new Carbon($user->started_at);
        $endedAt = new Carbon('2099-12-31');

        if (!empty($user->ended_at)) {
            $endedAt = new Carbon($user->ended_at . ' 23:59:59');
        }

        if (!$today->between($startedAt, $endedAt)) {
            throw new AuthenticationException();
        }

        if ($user->aggregate_setting_region_id === 1) {
            $sponsor->permissions->{'smart2::program_period_average::view'} =
                ['contract' => ['start' => '1900-01-01 00:00:00', 'end' => '9999-12-31 23:59:59']];
            $sponsor->permissions->{'smart2::sample_count::view'} =
                ['contract' => ['start' => '1900-01-01 00:00:00', 'end' => '9999-12-31 23:59:59']];
        }

        if ($user->aggregate_setting_region_id === 2) {
            $sponsor->permissions->{'smart2::sample_count::view'} =
                ['contract' => ['start' => '1900-01-01 00:00:00', 'end' => '9999-12-31 23:59:59']];
        }

        $user->permissions = $sponsor->permissions;
        $user->trial_settings = $sponsor->trial_settings;

        $noticesService = new NoticesService();
        $user->notices = $noticesService($memberId);

        return $user;
    }

    public function changePassword(int $id, array $passwords)
    {
        $member = Member::find($id);

        if ($member->init_login_flag === \Config::get('const.INIT_LOGIN_FLAG.INITIAL')) {
            $member->init_login_flag = \Config::get('const.INIT_LOGIN_FLAG.ALREADY');
        }
        $member->password_digest = Hash::make($passwords['password']);
        $member->save();
        return true;
    }
}
