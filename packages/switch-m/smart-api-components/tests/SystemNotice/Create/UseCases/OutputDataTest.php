<?php

namespace Switchm\SmartApi\Components\Tests\SystemNotice\Get\UseCases;

use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

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
