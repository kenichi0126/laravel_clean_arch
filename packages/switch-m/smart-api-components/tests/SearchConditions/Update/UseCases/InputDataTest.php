<?php

namespace Switchm\SmartApi\Components\Tests\SearchConditions\Update\UseCases;

use Switchm\SmartApi\Components\SearchConditions\Update\UseCases\InputData;
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
            0,
            'test condition',
            '{\"test\": \"test\"}'
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'id' => 0,
            'name' => 'test condition',
            'condition' => '{\"test\": \"test\"}',
        ];

        $this->assertSame($expected['id'], $this->target->id());
        $this->assertSame($expected['name'], $this->target->name());
        $this->assertSame($expected['condition'], $this->target->condition());
    }
}
