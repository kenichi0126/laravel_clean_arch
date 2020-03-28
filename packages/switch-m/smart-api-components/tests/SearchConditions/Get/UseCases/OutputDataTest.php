<?php

namespace Switchm\SmartApi\Components\Tests\SearchConditions\Get\UseCases;

use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

/**
 * Class OutputDataTest.
 */
final class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            [
                [
                    'id' => 1,
                    'member_id' => 10,
                    'route_name' => 'main.test.test.1',
                    'condition' => '{\"test\": \"test\"}',
                    'created_at' => '2020-01-10 17:31:45',
                    'updated_at' => '2020-01-10 17:31:45',
                    'deleted_at' => null,
                ],
                [
                    'id' => 2,
                    'member_id' => 20,
                    'route_name' => 'main.test.test.2',
                    'condition' => '{\"test\": \"test\"}',
                    'created_at' => '2020-01-10 17:31:45',
                    'updated_at' => '2020-01-10 17:31:45',
                    'deleted_at' => null,
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
            'data' => [
                [
                    'id' => 1,
                    'member_id' => 10,
                    'route_name' => 'main.test.test.1',
                    'condition' => '{\"test\": \"test\"}',
                    'created_at' => '2020-01-10 17:31:45',
                    'updated_at' => '2020-01-10 17:31:45',
                    'deleted_at' => null,
                ],
                [
                    'id' => 2,
                    'member_id' => 20,
                    'route_name' => 'main.test.test.2',
                    'condition' => '{\"test\": \"test\"}',
                    'created_at' => '2020-01-10 17:31:45',
                    'updated_at' => '2020-01-10 17:31:45',
                    'deleted_at' => null,
                ],
            ],
        ];

        $this->assertSame($expected['data'], $this->target->data());
    }
}
