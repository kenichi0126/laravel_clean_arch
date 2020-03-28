<?php

namespace Tests\Unit\App\Http\UserInterfaces\PanelStructure\Get;

use App\Http\UserInterfaces\PanelStructure\Get\ListPresenter;
use Prophecy\Argument as arg;
use ReflectionClass;
use stdClass;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\OutputData;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ListPresenterTest extends TestCase
{
    private $presenterOutput;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->presenterOutput = $this->prophesize(PresenterOutput::class);

        $this->target = new ListPresenter($this->presenterOutput->reveal());
    }

    /**
     * @test
     * @dataProvider convertDefinitionTextDataProvider
     * @param $def
     * @param $condition_text
     * @param bool $isCustomDivision
     * @param $expected
     * @throws \ReflectionException
     */
    public function convertDefinitionText($def, $condition_text, bool $isCustomDivision, $expected): void
    {
        $definitionText = new stdClass();
        $definitionText->def = $def;
        $definitionText->condition_text = $condition_text;

        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('convertDefinitionText');
        $method->setAccessible(true);

        $actual = $method->invoke($this->target, $definitionText, $isCustomDivision);

        $this->assertSame($expected, $actual);
    }

    public function convertDefinitionTextDataProvider()
    {
        return [
            [null, null, false, ''],
            ['age=18', null, true, '年齢： 18才'],
            [null, '性別=男', true, '性別： 男'],
            [null, '性別=男', false, '男'],
        ];
    }

    /**
     * @test
     * @dataProvider invokeDataProvider
     * @param $attrDivs
     * @param $paneler_id
     * @param $division
     * @param $code
     * @param $number
     * @param $baseFiveDivisionFlag
     */
    public function invoke($attrDivs, $paneler_id, $division, $code, $number, $baseFiveDivisionFlag): void
    {
        $panelDataCommon = new stdClass();
        $panelDataCommon->paneler_id = '{1}';
        $panelDataCommon->division = $division;
        $panelDataCommon->code = 'personal';
        $panelDataCommon->number = 1;

        $panelData = new stdClass();
        $panelData->paneler_id = $paneler_id;
        $panelData->division = $division;
        $panelData->code = $code;
        $panelData->number = $number;

        $panelDataCustom = new stdClass();
        $panelDataCustom->paneler_id = '{1}';
        $panelDataCustom->division = 'custom123';
        $panelDataCustom->code = '123';
        $panelDataCustom->number = 1;

        $output = new OutputData($attrDivs, [$panelDataCommon, $panelData, $panelDataCustom], $baseFiveDivisionFlag);

        $output->attrDivs()['base'][$panelData->division][$panelData->code]['definition_text'][0]->def = '';
        $output->attrDivs()['base'][$panelData->division][$panelData->code]['definition_text'][0]->condition_text = '';

        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }

    public function invokeDataProvider()
    {
        return [
            [
                [
                    'base' => [
                        'ga12' => [
                            'fc' => [
                                'display_order' => 0,
                                'division' => 'ga12',
                                'code' => 'fc',
                                'name' => 'FC',
                                'definition' => 'gender=f:age=4-12',
                                  'definition_text' => [new stdClass()],
                                'restore_info' => null,
                                  'restore_info_text' => 'test',
                            ],
                        ],
                    ],
                    'custom' => [],
                ],
                '{1}', 'ga12', 'fc', 1, false,
            ],
            [
                [
                    'base' => [
                        'ga8' => [
                            'c' => [
                                'display_order' => 0,
                                'division' => 'ga8',
                                'code' => 'c',
                                'name' => 'C',
                                'definition' => 'age=4-12',
                                'definition_text' => [new stdClass()],
                                'restore_info' => null,
                                'restore_info_text' => null,
                            ],
                        ],
                    ],
                    'custom' => [],
                ],
                '', 'ga8', 'c', 1, true,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider invokeCustomDataProvider
     * @param $attrDivs
     * @param $paneler_id
     * @param $division
     * @param $code
     * @param $number
     * @param $baseFiveDivisionFlag
     */
    public function invoke_custom($attrDivs, $paneler_id, $division, $code, $number, $baseFiveDivisionFlag): void
    {
        $panelDataCommon = new stdClass();
        $panelDataCommon->paneler_id = '{1}';
        $panelDataCommon->division = $division;
        $panelDataCommon->code = 'personal';
        $panelDataCommon->number = 1;

        $panelData = new stdClass();
        $panelData->paneler_id = $paneler_id;
        $panelData->division = $division;
        $panelData->code = $code;
        $panelData->number = $number;

        $panelDataCustom = new stdClass();
        $panelDataCustom->paneler_id = '{1}';
        $panelDataCustom->division = 'custom123';
        $panelDataCustom->code = '123';
        $panelDataCustom->number = 1;

        $output = new OutputData($attrDivs, [$panelDataCommon, $panelData, $panelDataCustom], $baseFiveDivisionFlag);

        $output->attrDivs()['base'][$panelData->division][$panelData->code]['definition_text'][0]->def = '';
        $output->attrDivs()['base'][$panelData->division][$panelData->code]['definition_text'][0]->condition_text = '';

        $output->attrDivs()['custom']['custom123']['123']['definition_text'][0]->def = '';
        $output->attrDivs()['custom']['custom123']['123']['definition_text'][0]->condition_text = '';

        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }

    public function invokeCustomDataProvider()
    {
        return [
            [
                [
                    'base' => [
                        'ga8' => [
                            'c' => [
                                'display_order' => 0,
                                'division' => 'ga8',
                                'code' => 'c',
                                'name' => 'C',
                                'definition' => 'age=4-12',
                                'definition_text' => [new stdClass()],
                                'restore_info' => null,
                                'restore_info_text' => null,
                            ],
                        ],
                    ],
                    'custom' => [
                        'custom123' => [
                            '123' => [
                                'display_order' => 1,
                                'division' => 'custom123',
                                'code' => '123',
                                'name' => 'てすと',
                                'definition' => 'age=4-99:paneler_id=2',
                                'definition_text' => [new stdClass()],
                                'restore_info' => 'test',
                                'restore_info_text' => 'test',
                            ],
                        ],
                    ],
                ],

            '', 'ga8', 'c', 1, false,
            ],
        ];
    }

    /**
     * @test
     */
    public function invoke_abort(): void
    {
        $output = new OutputData([], [], true);

        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldNotBeCalled();

        $this->expectException(NotFoundHttpException::class);

        $this->target->__invoke($output);
    }
}
