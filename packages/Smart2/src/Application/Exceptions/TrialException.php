<?php

namespace Smart2\Application\Exceptions;

// TODO: takata/移行が完了したら削除する
class TrialException extends BusinessException
{
    public function __construct()
    {
        $setting = \Auth::getUser()->sponsor->sponsorTrial->settings;

        $from = new \Carbon\Carbon($setting['search_range']['start']);
        $to = new \Carbon\Carbon($setting['search_range']['end']);

        $strFrom = $from->format('Y-m-d');
        $strTo = $to->format('Y-m-d');

        $this->message = "トライアル期間は ${strFrom} - ${strTo} の期間のみ検索可能です。";
    }
}
