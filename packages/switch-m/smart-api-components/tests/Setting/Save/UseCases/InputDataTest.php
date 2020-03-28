<?php

namespace Switchm\SmartApi\Components\Tests\Setting\Save\UseCases;

use Switchm\SmartApi\Components\Setting\Save\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

/**
 * Class InputDataTest.
 */
class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            1,
            '',
            [],
            1,
            1
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'secFlag' => 1,
            'division' => '',
            'codes' => [],
            'regionId' => 1,
            'id' => 1,
        ];

        $this->assertSame($expected['secFlag'], $this->target->secFlag());
        $this->assertSame($expected['division'], $this->target->division());
        $this->assertSame($expected['codes'], $this->target->codes());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['id'], $this->target->id());
    }
}
