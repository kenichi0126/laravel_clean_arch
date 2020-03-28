<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\PanelStructureDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class PanelStructureDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(PanelStructureDao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider getPanelDataDataProvider
     * @param array $attrDivs
     * @param int $regionId
     * @param array $bindings
     */
    public function getPanelData(array $attrDivs, int $regionId, array $bindings): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('createConditionOriginalDivSql')
            ->with(Mockery::any(), Mockery::any(), Mockery::any(), Mockery::any())
            ->andReturn('')
            ->once();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->with(Mockery::any(), $bindings)
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getPanelData($attrDivs, $regionId);

        $this->assertEquals($expected, $actual);
    }

    public function getPanelDataDataProvider()
    {
        $cases = ['case1', 'case2'];
        $attrDivs = [
            [
                'base' => [
                    'ga12' => [
                        'fc' => [
                            'display_order' => 1,
                            'division' => 'ga12',
                            'code' => 'fc',
                            'name' => 'FC',
                            'definition' => 'gender=f:age=4-12',
                            'definition_text' => [
                                (object) [
                                    'def' => 'age=4-12',
                                    'condition_text' => '',
                                ],
                                (object) [
                                    'def' => 'gender=f',
                                    'condition_text' => '性別=女性',
                                ],
                            ],
                            'restore_info' => null,
                            'restore_info_text' => null,
                        ],
                    ],
                ],
            ],
            [
                'custom' => [
                    'anything' => [
                        'syokuseikatsu' => [
                            'display_order' => 1,
                            'division' => 'anything',
                            'code' => 'syokuseikatsu',
                            'name' => '食生活',
                            'definition' => 'age=20-34:paneler_id=14135',
                            'definition_text' => [
                                    (object) [
                                        'def' => 'age=20-34',
                                        'condition_text' => '',
                                    ],
                                ],
                            'restore_info' => null,
                            'restore_info_text' => '条件１ and 条件２に該当する男女',
                        ],
                    ],
                ],
                'base' => [
                    'ga12' => [
                        'fc' => [
                            'display_order' => 1,
                            'division' => 'ga12',
                            'code' => 'fc',
                            'name' => 'FC',
                            'definition' => 'gender=f:age=4-12',
                            'definition_text' => [
                                (object) [
                                    'def' => 'age=4-12',
                                    'condition_text' => '',
                                ],
                                (object) [
                                    'def' => 'gender=f',
                                    'condition_text' => '性別=女性',
                                ],
                            ],
                            'restore_info' => null,
                            'restore_info_text' => null,
                        ],
                    ],
                ],
            ],
        ];
        $regionId = [1, 2];
        $bindings = [
            [
                ':regionId0' => '1',
                ':division0' => 'ga12',
                ':code0' => 'personal',
                ':code1' => 'household',
                ':code2' => 'fc',
            ], [
                ':regionId0' => '2', ':division0' => 'ga12', ':code0' => 'personal', ':code1' => 'household', ':code2' => 'fc', ':division1' => 'anything', ':code3' => 'syokuseikatsu',
            ],
        ];

        foreach ($cases as $i => $case) {
            yield [
                $attrDivs[$i],
                $regionId[$i],
                $bindings[$i],
            ];
        }
    }
}
