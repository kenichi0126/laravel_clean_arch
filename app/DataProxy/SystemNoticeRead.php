<?php

namespace App\DataProxy;

use App\Eloquent\SystemNoticesRead;

class SystemNoticeRead extends BaseEloquent implements SystemNoticeReadInterface
{
    public function __construct(SystemNoticesRead $model)
    {
        $this->model = $model;
    }
}
