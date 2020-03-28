<?php

namespace Switchm\SmartApi\Components\Tests\CmMaterials\Get\UseCases;

use Switchm\SmartApi\Components\CmMaterials\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            [1],
            '2019-01-01 05:00:00',
            '2019-01-01 05:00:00',
            5,
            10,
            5,
            10,
            1,
            [1],
            0,
            1,
            [1],
            [1]
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'productIds' => [1],
            'startDate' => '2019-01-01 05:00:00',
            'endDate' => '2019-01-01 05:00:00',
            'startTimeHour' => 5,
            'startTimeMin' => 10,
            'endTimeHour' => 5,
            'endTimeMin' => 10,
            'regionId' => 1,
            'channels' => [1],
            'cmType' => 0,
            'cmSeconds' => 1,
            'companyIds' => [1],
            'progIds' => [1],
        ];

        $this->assertSame($expected['productIds'], $this->target->productIds());
        $this->assertSame($expected['startDate'], $this->target->startDate());
        $this->assertSame($expected['endDate'], $this->target->endDate());
        $this->assertSame($expected['startTimeHour'], $this->target->startTimeHour());
        $this->assertSame($expected['startTimeMin'], $this->target->startTimeMin());
        $this->assertSame($expected['endTimeHour'], $this->target->endTimeHour());
        $this->assertSame($expected['endTimeMin'], $this->target->endTimeMin());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['cmType'], $this->target->cmType());
        $this->assertSame($expected['cmSeconds'], $this->target->cmSeconds());
    }
}
