<?php

namespace Switchm\SmartApi\Components\Tests\Top\Get\UseCases;

use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\Top\Get\UseCases\OutputData;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            '',
            [],
            [],
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
            'date' => '',
            'programs' => [],
            'charts' => [],
            'categories' => [],
            'phNumbers' => [],
        ];

        $this->assertSame($expected['date'], $this->target->date());
        $this->assertSame($expected['programs'], $this->target->programs());
        $this->assertSame($expected['charts'], $this->target->charts());
        $this->assertSame($expected['categories'], $this->target->categories());
        $this->assertSame($expected['phNumbers'], $this->target->phNumbers());
    }
}
