<?php

namespace Switchm\SmartApi\Components\Tests\UserNotice\Get\UseCases;

use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\UserNotice\Create\UseCases\OutputData;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData();
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $this->assertEquals($this->target, new OutputData());
    }
}
