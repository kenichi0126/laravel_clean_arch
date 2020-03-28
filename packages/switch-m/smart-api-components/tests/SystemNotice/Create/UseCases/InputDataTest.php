<?php

namespace Switchm\SmartApi\Components\Tests\SystemNotice\Get\UseCases;

use Switchm\SmartApi\Components\SystemNotice\Create\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            1,
            2
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'noticeId' => 1,
            'memberId' => 2,
        ];

        $this->assertSame($expected['noticeId'], $this->target->noticeId());
        $this->assertEquals($expected['memberId'], $this->target->memberId());
    }
}
