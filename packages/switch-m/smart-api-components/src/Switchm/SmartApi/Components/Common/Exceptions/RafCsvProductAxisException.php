<?php

namespace Switchm\SmartApi\Components\Common\Exceptions;

class RafCsvProductAxisException extends BusinessException
{
    public function __construct(int $number)
    {
        $this->message = "集計軸に商品別を指定する場合、商品の数が${number}以内になるように絞り込みをしてください。";
    }
}
