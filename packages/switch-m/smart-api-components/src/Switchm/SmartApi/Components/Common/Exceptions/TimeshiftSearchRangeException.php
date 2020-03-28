<?php

namespace Switchm\SmartApi\Components\Common\Exceptions;

class TimeshiftSearchRangeException extends BusinessException
{
    public function __construct(string $minDate)
    {
        $this->message = "期間は${minDate}～放送日より7日前以上開けて指定してください。※タイムシフト／総合視聴率を含む場合";
    }
}
