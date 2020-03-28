<?php

namespace Switchm\SmartApi\Components\Tests\TimeBox\Get\UseCases;

use Smart2\CommandModel\Eloquent\Member;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\InputData;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            1,
            new Member()
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'regionId' => 1,
            'trialSettings' => new Member(),
        ];

        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertEquals($expected['trialSettings'], $this->target->trialSettings());
    }
}
