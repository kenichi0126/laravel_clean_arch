<?php

namespace Switchm\SmartApi\Components\Tests\SearchConditions\Create\UseCases;

use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

final class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(true);
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = true;
        $this->assertSame($expected, $this->target->result());
    }
}
