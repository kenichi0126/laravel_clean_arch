<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases\InputData;
use Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\MemberOriginalDivDao;

class InteractorTest extends TestCase
{
    private $memberOriginalDivDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->memberOriginalDivDao = $this->prophesize(MemberOriginalDivDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->memberOriginalDivDao->reveal(),
            $this->outputBoundary->reveal()
        );
    }

    /**
     * @test
     */
    public function invoke_selectWithMenu_empty(): void
    {
        $input = new InputData(
            1,
            1
        );

        $outputData = new OutputData([]);

        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_selectDivisions_empty(): void
    {
        $selectWithMenuReturn = [
            (object) [
                'member_id' => 1230,
                'menu' => 'setting',
                'division' => 'custom1230_2020012215551_1',
                'target_date_from' => '2013-12-30',
                'target_date_to' => '2099-12-31',
                'display_order' => 1,
                'original_div_edit_flag' => 1,
            ],
        ];

        $input = new InputData(
            1,
            1
        );

        $outputData = new OutputData([]);

        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn($selectWithMenuReturn)
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectDivisions(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_selectCodes_empty(): void
    {
        $selectWithMenuReturn = [
            (object) [
                'member_id' => 1230,
                'menu' => 'setting',
                'division' => 'custom1230_2020012215551_1',
                'target_date_from' => '2013-12-30',
                'target_date_to' => '2099-12-31',
                'display_order' => 1,
                'original_div_edit_flag' => 1,
            ],
        ];

        $selectDivisionsReturn = [
            (object) [
                'division_name' => 'カスタム1',
                'menu' => '時間帯',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 1,
            ],
            (object) [
                'division_name' => 'カスタム1',
                'menu' => '番組',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 2,
            ],
            (object) [
                'division_name' => 'カスタム1',
                'menu' => 'CM',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 3,
            ],
            (object) [
                'division_name' => 'カスタム1',
                'menu' => 'R&F',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 4,
            ],
        ];

        $input = new InputData(
            1,
            1
        );

        $outputData = new OutputData(
            [
                'custom1230_2020012215551_1' => [
                    'data' => (object) [
                        'division_name' => 'カスタム1',
                        'menu' => '時間帯',
                        'period' => '2013-12-30～2099-12-31',
                        'original_div_edit_flag' => 0,
                        'division' => 'custom1230_2020012215551_1',
                        'menu_order' => 1,
                        'codes' => [],
                    ],
                    'menus' => [
                        [
                            'menu' => '全メニュー',
                            'period' => '2013-12-30～2099-12-31',
                        ],
                    ],
                    'original_div_edit_flag' => 1,
                    'rowspan' => 1,
                ],
            ]
        );

        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn($selectWithMenuReturn)
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectDivisions(arg::cetera())
            ->willReturn($selectDivisionsReturn)
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectCodes(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_childage_f(): void
    {
        $selectWithMenuReturn = [
            (object) [
                'member_id' => 1230,
                'menu' => 'setting',
                'division' => 'custom1230_2020012215551_1',
                'target_date_from' => '2013-12-30',
                'target_date_to' => '2099-12-31',
                'display_order' => 1,
                'original_div_edit_flag' => 1,
            ],
        ];

        $selectDivisionsReturn = [
            (object) [
                'division_name' => 'カスタム1',
                'menu' => '時間帯',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 1,
            ],
            (object) [
                'division_name' => 'カスタム1',
                'menu' => '番組',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 2,
            ],
            (object) [
                'division_name' => 'カスタム1',
                'menu' => 'CM',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 3,
            ],
            (object) [
                'division_name' => 'カスタム1',
                'menu' => 'R&F',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 4,
            ],
        ];

        $selectCodes = [
            (object) [
                'division' => 'custom1230_2020012215551_1',
                'code' => '202001241709425211230',
                'name' => 'test',
                'definition' => 'gender=f:age=4-99:occupation=1:childage_f=4-99:married=2:paneler_id=1,2',
                'restore_info' => 'i1=1,2:connection_division=or',
                'restore_info_text' => '条件 1： [i1]家族構成・・・1.単身世帯、2.夫婦のみの世帯',
            ],
        ];

        $selectDefinitionText = [
            (object) [
                'condition_text' => '性別=女性',
            ],
            (object) [
                'condition_text' => '職業=公務員',
            ],
            (object) [
                'condition_text' => '未既婚=既婚',
            ],
        ];

        $input = new InputData(
            1,
            1
        );

        $outputData = new OutputData(
            [
                'custom1230_2020012215551_1' => [
                    'data' => (object) [
                        'division_name' => 'カスタム1',
                        'menu' => '時間帯',
                        'period' => '2013-12-30～2099-12-31',
                        'original_div_edit_flag' => 0,
                        'division' => 'custom1230_2020012215551_1',
                        'menu_order' => 1,
                        'codes' => [
                            (object) [
                                'division' => 'custom1230_2020012215551_1',
                                'code' => '202001241709425211230',
                                'name' => 'test',
                                'definition' => 'gender=f:age=4-99:occupation=1:childage_f=4-99:married=2:paneler_id=1,2',
                                'restore_info' => 'i1=1,2:connection_division=or',
                                'restore_info_text' => '条件 1： [i1]家族構成・・・1.単身世帯、2.夫婦のみの世帯',
                                'texts' => [
                                    0 => null,
                                    1 => '年齢： 4-99才',
                                    2 => null,
                                    3 => '子供性別： 女性',
                                    4 => '子供年齢： 4-99才',
                                    5 => null,
                                ],
                            ],
                        ],
                    ],
                    'menus' => [
                        [
                            'menu' => '全メニュー',
                            'period' => '2013-12-30～2099-12-31',
                        ],
                    ],
                    'original_div_edit_flag' => 1,
                    'rowspan' => 1,
                ],
            ]
        );

        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn($selectWithMenuReturn)
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectDivisions(arg::cetera())
            ->willReturn($selectDivisionsReturn)
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectCodes(arg::cetera())
            ->willReturn($selectCodes)
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectDefinitionText(arg::cetera())
            ->willReturn($selectDefinitionText)
            ->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_childage_m(): void
    {
        $selectWithMenuReturn = [
            (object) [
                'member_id' => 1230,
                'menu' => 'setting',
                'division' => 'custom1230_2020012215551_1',
                'target_date_from' => '2013-12-30',
                'target_date_to' => '2099-12-31',
                'display_order' => 1,
                'original_div_edit_flag' => 1,
            ],
        ];

        $selectDivisionsReturn = [
            (object) [
                'division_name' => 'カスタム1',
                'menu' => '時間帯',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 1,
            ],
            (object) [
                'division_name' => 'カスタム1',
                'menu' => '番組',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 2,
            ],
            (object) [
                'division_name' => 'カスタム1',
                'menu' => 'CM',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 3,
            ],
            (object) [
                'division_name' => 'カスタム1',
                'menu' => 'R&F',
                'period' => '2013-12-30～2099-12-31',
                'original_div_edit_flag' => 0,
                'division' => 'custom1230_2020012215551_1',
                'menu_order' => 4,
            ],
        ];

        $selectCodes = [
            (object) [
                'division' => 'custom1230_2020012215551_1',
                'code' => '202001241709425211230',
                'name' => 'test',
                'definition' => 'gender=f:age=4-99:occupation=1:childage_m=4-99:married=2:paneler_id=1,2',
                'restore_info' => 'i1=1,2:connection_division=or',
                'restore_info_text' => '条件 1： [i1]家族構成・・・1.単身世帯、2.夫婦のみの世帯',
            ],
        ];

        $selectDefinitionText = [
            (object) [
                'condition_text' => '性別=女性',
            ],
            (object) [
                'condition_text' => '職業=公務員',
            ],
            (object) [
                'condition_text' => '未既婚=既婚',
            ],
        ];

        $input = new InputData(
            1,
            1
        );

        $outputData = new OutputData(
            [
                'custom1230_2020012215551_1' => [
                    'data' => (object) [
                        'division_name' => 'カスタム1',
                        'menu' => '時間帯',
                        'period' => '2013-12-30～2099-12-31',
                        'original_div_edit_flag' => 0,
                        'division' => 'custom1230_2020012215551_1',
                        'menu_order' => 1,
                        'codes' => [
                            (object) [
                                'division' => 'custom1230_2020012215551_1',
                                'code' => '202001241709425211230',
                                'name' => 'test',
                                'definition' => 'gender=f:age=4-99:occupation=1:childage_m=4-99:married=2:paneler_id=1,2',
                                'restore_info' => 'i1=1,2:connection_division=or',
                                'restore_info_text' => '条件 1： [i1]家族構成・・・1.単身世帯、2.夫婦のみの世帯',
                                'texts' => [
                                    0 => null,
                                    1 => '年齢： 4-99才',
                                    2 => null,
                                    3 => '子供性別： 男性',
                                    4 => '子供年齢： 4-99才',
                                    5 => null,
                                ],
                            ],
                        ],
                    ],
                    'menus' => [
                        [
                            'menu' => '全メニュー',
                            'period' => '2013-12-30～2099-12-31',
                        ],
                    ],
                    'original_div_edit_flag' => 1,
                    'rowspan' => 1,
                ],
            ]
        );

        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn($selectWithMenuReturn)
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectDivisions(arg::cetera())
            ->willReturn($selectDivisionsReturn)
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectCodes(arg::cetera())
            ->willReturn($selectCodes)
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectDefinitionText(arg::cetera())
            ->willReturn($selectDefinitionText)
            ->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }
}
