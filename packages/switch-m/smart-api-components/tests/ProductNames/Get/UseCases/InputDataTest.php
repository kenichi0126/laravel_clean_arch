<?php

namespace Switchm\SmartApi\Components\Tests\ProductNames\Get\UseCases;

use Switchm\SmartApi\Components\ProductNames\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-07 04:59:59',
            [0],
            'iPhone',
            [],
            [1],
            [],
            [3, 4, 5, 6, 7],
            0,
            1,
            []
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-07 04:59:59',
            'dataType' => [0],
            'productName' => 'iPhone',
            'companyIds' => [],
            'regionIds' => [1],
            'productIds' => [],
            'channels' => [3, 4, 5, 6, 7],
            'cmType' => 0,
            'cmSeconds' => 1,
            'progIds' => [],
        ];

        $this->assertSame($expected['startDateTime'], $this->target->startDateTime());
        $this->assertSame($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['productName'], $this->target->productName());
        $this->assertSame($expected['companyIds'], $this->target->companyIds());
        $this->assertSame($expected['regionIds'], $this->target->regionIds());
        $this->assertSame($expected['productIds'], $this->target->productIds());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['cmType'], $this->target->cmType());
        $this->assertSame($expected['cmSeconds'], $this->target->cmSeconds());
        $this->assertSame($expected['progIds'], $this->target->progIds());
    }
}
