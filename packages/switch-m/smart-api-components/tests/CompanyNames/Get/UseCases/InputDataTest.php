<?php

namespace Switchm\SmartApi\Components\Tests\CompanyNames\Get\UseCases;

use Switchm\SmartApi\Components\CompanyNames\Get\UseCases\InputData;
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
            'ソフトバンク',
            [],
            1,
            [],
            [3, 4, 5, 6, 7],
            0,
            1,
            [],
            [0]
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
            'companyName' => 'ソフトバンク',
            'progIds' => [],
            'regionId' => 1,
            'companyId' => [],
            'channels' => [3, 4, 5, 6, 7],
            'cmType' => 0,
            'cmSeconds' => 1,
            'productIds' => [],
            'dataType' => [0],
        ];

        $this->assertSame($expected['startDateTime'], $this->target->startDateTime());
        $this->assertSame($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['companyName'], $this->target->companyName());
        $this->assertSame($expected['progIds'], $this->target->progIds());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['companyId'], $this->target->companyId());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['cmType'], $this->target->cmType());
        $this->assertSame($expected['cmSeconds'], $this->target->cmSeconds());
        $this->assertSame($expected['productIds'], $this->target->productIds());
        $this->assertSame($expected['dataType'], $this->target->dataType());
    }
}
