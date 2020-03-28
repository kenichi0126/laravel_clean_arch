<?php

namespace Switchm\SmartApi\Components\Tests\PanelStructure\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\InputData;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\MemberOriginalDivDao;
use Switchm\SmartApi\Queries\Dao\Rdb\PanelStructureDao;

class InteractorTest extends TestCase
{
    private $panelStructureDao;

    private $memberOriginalDivDao;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->panelStructureDao = $this->prophesize(PanelStructureDao::class);
        $this->memberOriginalDivDao = $this->prophesize(MemberOriginalDivDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor($this->panelStructureDao->reveal(), $this->memberOriginalDivDao->reveal(), $this->outputBoundary->reveal());
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function convertAttrDivs_no_data(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('convertAttrDivs');
        $method->setAccessible(true);

        $this->memberOriginalDivDao
            ->selectDefinitionText(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $expected = [];

        $actual = $method->invoke(
            $this->target,
            []
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws \ReflectionException
     */
    public function convertAttrDivs(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('convertAttrDivs');
        $method->setAccessible(true);

        $this->memberOriginalDivDao
            ->selectDefinitionText(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $expected = [
            'ga8' => [
                'f1' => [
                    'display_order' => 1,
                    'division' => 'ga8',
                    'code' => 'f1',
                    'name' => 'F1',
                    'definition' => 'gender=f',
                    'definition_text' => [],
                    'restore_info' => null,
                    'restore_info_text' => null,
                ],
            ],
        ];

        $attrDiv = new \stdClass();
        $attrDiv->division = 'ga8';
        $attrDiv->code = 'f1';
        $attrDiv->name = 'F1';
        $attrDiv->definition = 'gender=f';
        $attrDiv->definitionText = 'test';
        $attrDiv->restore_info = null;
        $attrDiv->restore_info_text = null;

        $actual = $method->invoke(
            $this->target,
            [$attrDiv]
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function invoke_all_if_false(): void
    {
        $this->memberOriginalDivDao
            ->selectDefinitionText(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->memberOriginalDivDao
            ->selectCodes(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->panelStructureDao
            ->getPanelData(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['base' => []], [], false))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            'ga8',
            1,
            false,
            1
        );

        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_all_if_true(): void
    {
        $this->memberOriginalDivDao
            ->selectDefinitionText(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $attrDiv = new \stdClass();
        $attrDiv->division = 'custom';
        $attrDiv->code = 'custom';
        $attrDiv->name = 'custom';
        $attrDiv->definition = 'gender=f';
        $attrDiv->definitionText = 'test';
        $attrDiv->restore_info = null;
        $attrDiv->restore_info_text = null;

        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn([$attrDiv])
            ->shouldBeCalled();

        $this->memberOriginalDivDao
            ->selectCodes(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->panelStructureDao
            ->getPanelData(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(['base' => [], 'custom' => []], [], true))
            ->willReturn()
            ->shouldBeCalled();

        $input = new InputData(
            'ga8',
            1,
            true,
            1
        );

        $this->target->__invoke($input);
    }
}
