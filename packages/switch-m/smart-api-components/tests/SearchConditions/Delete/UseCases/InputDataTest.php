<?php

namespace Switchm\SmartApi\Components\Tests\SearchConditions\Delete\UseCases;

use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\InputData;
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
            0
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'id' => 0,
        ];

        $this->assertSame($expected['id'], $this->target->id());
    }
}
