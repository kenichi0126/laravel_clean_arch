<?php

namespace Switchm\SmartApi\Components\Tests\SettingAggregate\Get\UseCases;

use Switchm\SmartApi\Components\SettingAggregate\Get\UseCases\InputData;
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
            new \stdClass()
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = new \stdClass();

        $this->assertEquals($expected, $this->target->userInfo());
    }
}
