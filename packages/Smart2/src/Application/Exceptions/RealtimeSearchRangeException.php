<?php

namespace Smart2\Application\Exceptions;

// TODO: takata/移行が完了したら削除する
class RealtimeSearchRangeException extends BusinessException
{
    public function __construct(string $minDate)
    {
        $this->message = "期間は${minDate}以降で指定してください。";
    }
}
