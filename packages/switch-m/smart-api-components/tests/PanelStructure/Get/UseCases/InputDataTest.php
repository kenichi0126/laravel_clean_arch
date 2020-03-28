<?php

namespace Switchm\SmartApi\Components\Tests\PanelStructure\Get\UseCases;

use Switchm\SmartApi\Components\PanelStructure\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            'ga8',
            1,
            true,
            1
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'division' => 'ga8',
            'regionId' => 1,
            'isBaseFiveDivision' => true,
            'userId' => 1,
        ];

        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['isBaseFiveDivision'], $this->target->isBaseFiveDivision());
        $this->assertSame($expected['userId'], $this->target->userId());
    }
}
