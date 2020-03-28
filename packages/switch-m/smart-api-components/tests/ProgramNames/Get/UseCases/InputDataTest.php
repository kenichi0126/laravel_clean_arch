<?php

namespace Switchm\SmartApi\Components\Tests\ProgramNames\Get\UseCases;

use Switchm\SmartApi\Components\ProgramNames\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            '2019-01-01 05:00:00',
            '2019-01-31 04:59:00',
            '世界',
            [],
            'digital',
            true,
            [1, 2, 3, 4, 5, 6, 7],
            [15, 16, 17, 18, 19, 20, 21],
            [22, 23, 24, 25],
            0,
            '1',
            [],
            [],
            1,
            [0],
            [],
            ['1', '2', '3', '4', '5', '6', '0'],
            true
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'startDateTime' => '2019-01-01 05:00:00',
            'endDateTime' => '2019-01-31 04:59:00',
            'programName' => '世界',
            'channels' => [],
            'digitalAndBs' => 'digital',
            'programFlag' => true,
            'digitalKanto' => [1, 2, 3, 4, 5, 6, 7],
            'bs1' => [15, 16, 17, 18, 19, 20, 21],
            'bs2' => [22, 23, 24, 25],
            'cmtype' => 0,
            'cmSeconds' => '1',
            'productIds' => [],
            'companies' => [],
            'regionId' => 1,
            'dataType' => [0],
            'programIds' => [],
            'wdays' => ['1', '2', '3', '4', '5', '6', '0'],
            'holiday' => true,
        ];

        $this->assertSame($expected['startDateTime'], $this->target->startDateTime());
        $this->assertSame($expected['endDateTime'], $this->target->endDateTime());
        $this->assertSame($expected['programName'], $this->target->programName());
        $this->assertSame($expected['channels'], $this->target->channels());
        $this->assertSame($expected['digitalAndBs'], $this->target->digitalAndBs());
        $this->assertSame($expected['programFlag'], $this->target->programFlag());
        $this->assertSame($expected['digitalKanto'], $this->target->digitalKanto());
        $this->assertSame($expected['bs1'], $this->target->bs1());
        $this->assertSame($expected['bs2'], $this->target->bs2());
        $this->assertSame($expected['cmtype'], $this->target->cmtype());
        $this->assertSame($expected['cmSeconds'], $this->target->cmSeconds());
        $this->assertSame($expected['productIds'], $this->target->productIds());
        $this->assertSame($expected['companies'], $this->target->companies());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['dataType'], $this->target->dataType());
        $this->assertSame($expected['programIds'], $this->target->programIds());
        $this->assertSame($expected['wdays'], $this->target->wdays());
        $this->assertSame($expected['holiday'], $this->target->holiday());
    }
}
