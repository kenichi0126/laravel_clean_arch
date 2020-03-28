<?php

namespace Switchm\SmartApi\Components\Common\Exceptions;

class AttrDivCreationLimitOverException extends BusinessException
{
    public function __construct()
    {
        $this->message = '条件を保存できる数の限界を超えています。他のユーザーによって追加された可能性があります。';
    }
}
