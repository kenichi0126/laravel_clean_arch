<?php

namespace Switchm\SmartApi\Components\Tests\TopRanking\Get\UseCases;

use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\InputData;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            1,
            '1',
            [1, 2, 3]
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'regionId' => 1,
            'conv15SecFlag' => '1',
            'broadcasterCompanyIds' => [1, 2, 3],
        ];

        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['conv15SecFlag'], $this->target->conv15SecFlag());
        $this->assertSame($expected['broadcasterCompanyIds'], $this->target->broadcasterCompanyIds());
    }
}
