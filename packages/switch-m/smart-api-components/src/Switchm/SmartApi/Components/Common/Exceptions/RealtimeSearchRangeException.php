<?php

namespace Switchm\SmartApi\Components\Common\Exceptions;

class RealtimeSearchRangeException extends BusinessException
{
    public function __construct(string $minDate)
    {
        $this->message = "期間は${minDate}以降で指定してください。";
    }
}
