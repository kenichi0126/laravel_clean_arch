<?php

namespace Switchm\SmartApi\Queries\Dao\Rdb;

use Switchm\SmartApi\Queries\Dao\AbstractPdoDao;

class Dao extends AbstractPdoDao
{
    protected $connectionName;

    public function __construct()
    {
        $this->connectionName = 'smart_read_rdb';
    }
}
