<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivsOrder\Update\UseCases;

use Switchm\SmartApi\Components\SettingAttrDivsOrder\Update\UseCases\InputData;
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
            [
                'division_test' => [
                    'code_test',
                ],
            ]
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'divisions' => [
                'division_test' => [
                    'code_test',
                ],
            ],
        ];

        $this->assertSame($expected['divisions'], $this->target->divisions());
    }
}
