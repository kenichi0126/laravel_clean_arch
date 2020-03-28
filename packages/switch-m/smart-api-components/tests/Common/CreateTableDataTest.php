<?php

namespace Switchm\SmartApi\Components\Tests\Common;

use ReflectionClass;
use ReflectionException;
use Switchm\SmartApi\Components\Common\CreateTableData;
use Switchm\SmartApi\Components\Tests\TestCase;

class CreateTableDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new CreateTableData();
    }

    /**
     * @test
     * @dataProvider avgHashExistsDataProvider
     * @param $avgHash
     * @param $channel
     * @param $dow
     * @param $key
     * @param $expected
     * @throws ReflectionException
     */
    public function avgHashExists($avgHash, $channel, $dow, $key, $expected): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('avgHashExists');
        $method->setAccessible(true);

        $actual = $method->invoke($this->target, $avgHash, $channel, $dow, $key);

        $this->assertEquals($expected, $actual);
    }

    public function avgHashExistsDataProvider()
    {
        return [
            [
                [],
                'channel',
                'dow',
                'key',
                false,
            ],
            [
                ['channel' => ['']],
                'channel',
                'dow',
                'key',
                false,
            ],
            [
                ['channel' => ['dow' => ['']]],
                'channel',
                'dow',
                'key',
                false,
            ],
            [
                ['channel' => ['dow' => ['key' => 1]]],
                'channel',
                'dow',
                'key',
                true,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider createTableDataDataProvider
     * @param mixed $data
     * @param mixed $channelIds
     * @param mixed $alias
     * @param mixed $dataDivision
     * @param mixed $limit
     * @param mixed $csvFlag
     * @param mixed $channelType
     * @param mixed $expected
     */
    public function createTableData($data, $channelIds, $alias, $dataDivision, $csvFlag, $channelType, $expected): void
    {
        $actual = ($this->target)($data, $channelIds, $alias, $dataDivision, $csvFlag, $channelType);

        $this->assertSame($expected, $actual);
    }

    public function createTableDataDataProvider()
    {
        return [
            [
                [
                    [
                    ],
                ],
                [1],
                '',
                '',
                '0',
                '0',
                [['hour' => '05', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '06', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '07', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '08', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '09', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '10', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '11', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '12', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '13', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '14', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '15', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '16', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '17', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '18', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '19', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '20', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '21', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '22', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '23', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '24', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '25', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '26', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '27', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '28', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'all', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'G', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'P', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'Av', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''], ],
            ],
            [
                [
                    [
                        'hhmm' => '00',
                        'dow' => 0,
                        'channel_id' => 1,
                        'viewing_rate' => 1.81684,
                    ],
                ],
                [1],
                '',
                '',
                '1',
                '0',
                [['hour' => '05:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '06:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '07:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '08:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '09:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '10:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '11:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '12:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '13:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '14:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '15:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '16:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '17:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '18:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '19:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '20:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '21:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '22:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '23:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '24:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '25:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '26:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '27:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '28:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '全日(6:00-23:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'G(19:00-21:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'P(19:00-22:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'AVG(5:00-28:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''], ],
            ],
            [
                [
                    [
                        'hhmm' => '00',
                        'dow' => 0,
                        'channel_id' => 1,
                        'viewing_rate' => 1.81684,
                    ],
                ],
                [1],
                'viewing_rate',
                'viewing_rate',
                '1',
                'bs1',
                [['hour' => '05:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '06:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '07:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '08:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '09:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '10:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '11:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '12:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '13:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '14:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '15:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '16:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '17:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '18:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '19:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '20:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '21:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '22:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '23:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '24:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82],
                    ['hour' => '25:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '26:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '27:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '28:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '全日(6:00-23:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'G(19:00-21:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'P(19:00-22:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => 'AVG(5:00-28:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82], ],
            ],
            [
                [
                    [
                        'hhmm' => '19',
                        'dow' => 0,
                        'channel_id' => 1,
                        'viewing_rate' => 1.81684,
                    ],
                ],
                [1],
                'viewing_rate',
                'viewing_rate',
                '1',
                'bs1',
                [['hour' => '05:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '06:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '07:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '08:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '09:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '10:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '11:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '12:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '13:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '14:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '15:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '16:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '17:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '18:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '19:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82],
                    ['hour' => '20:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '21:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '22:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '23:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '24:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '25:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '26:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '27:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '28:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '全日(6:00-23:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82],
                    ['hour' => 'G(19:00-21:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82],
                    ['hour' => 'P(19:00-22:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82],
                    ['hour' => 'AVG(5:00-28:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82], ],
            ],

            [
                [
                    [
                        'hhmm' => '19',
                        'dow' => 0,
                        'channel_id' => 1,
                        'viewing_rate' => 1.81684,
                    ],
                ],
                [1],
                'viewing_rate',
                'viewing_rate',
                '1',
                'bs1',
                [['hour' => '05:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '06:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '07:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '08:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '09:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '10:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '11:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '12:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '13:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '14:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '15:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '16:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '17:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '18:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '19:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82],
                    ['hour' => '20:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '21:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '22:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '23:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '24:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '25:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '26:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '27:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '28:00', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => ''],
                    ['hour' => '全日(6:00-23:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82],
                    ['hour' => 'G(19:00-21:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82],
                    ['hour' => 'P(19:00-22:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82],
                    ['hour' => 'AVG(5:00-28:59)', 11 => '', 12 => '', 13 => '', 14 => '', 15 => '', 16 => '', 10 => 1.82], ],
            ],
        ];
    }
}
