<?php

namespace Switchm\SmartApi\Components\Tests\Divisions\Get\UseCases;

use Switchm\SmartApi\Components\Divisions\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            [],
            []
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'divisions' => [],
            'divisionMaps' => [],
        ];

        $this->assertSame($expected['divisions'], $this->target->divisions());
        $this->assertSame($expected['divisionMaps'], $this->target->divisionMaps());
    }
}
