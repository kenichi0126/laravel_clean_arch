<?php

namespace Switchm\SmartApi\Components\Tests\PanelStructure\Get\UseCases;

use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            [],
            [],
            false
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'attrDivs' => [],
            'panelData' => [],
            'baseFiveDivisionFlag' => false,
        ];

        $this->assertSame($expected['attrDivs'], $this->target->attrDivs());
        $this->assertSame($expected['panelData'], $this->target->panelData());
        $this->assertSame($expected['baseFiveDivisionFlag'], $this->target->baseFiveDivisionFlag());
    }
}
