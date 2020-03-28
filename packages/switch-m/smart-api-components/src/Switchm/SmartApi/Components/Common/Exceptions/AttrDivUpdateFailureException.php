<?php

namespace Switchm\SmartApi\Components\Common\Exceptions;

class AttrDivUpdateFailureException extends BusinessException
{
    public function __construct()
    {
        $this->message = '条件を更新できませんでした。他のユーザーによって削除された可能性があります。';
    }
}
