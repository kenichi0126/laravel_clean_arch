<?php

namespace Switchm\SmartApi\Components\Tests\SearchConditions\Create\UseCases;

use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

/**
 * Class InputDataTest.
 */
final class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            1,
            1,
            'test condition',
            'main.test.test.1',
            '{\"test\": \"test\"}'
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'memberId' => 1,
            'regionId' => 1,
            'name' => 'test condition',
            'routeName' => 'main.test.test.1',
            'condition' => '{\"test\": \"test\"}',
        ];

        $this->assertSame($expected['memberId'], $this->target->memberId());
        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['name'], $this->target->name());
        $this->assertSame($expected['routeName'], $this->target->routeName());
        $this->assertSame($expected['condition'], $this->target->condition());
    }
}
