<?php

namespace Switchm\SmartApi\Components\Common\Exceptions;

use Carbon\Carbon;

class TrialException extends BusinessException
{
    public function __construct($user)
    {
        $setting = $user->sponsor->sponsorTrial->settings;

        $from = new Carbon($setting['search_range']['start']);
        $to = new Carbon($setting['search_range']['end']);

        $strFrom = $from->format('Y-m-d');
        $strTo = $to->format('Y-m-d');

        $this->message = "トライアル期間は ${strFrom} - ${strTo} の期間のみ検索可能です。";
    }
}
