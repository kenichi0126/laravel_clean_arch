<?php

namespace Switchm\SmartApi\Components\Tests\TopRanking\Get\UseCases;

use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Components\TopRanking\Get\UseCases\OutputData;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new OutputData(
            [],
            [],
            [],
            '',
            '',
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
            'program' => [],
            'company_cm' => [],
            'product_cm' => [],
            'programDate' => '',
            'cmDate' => '',
            'programPhNumbers' => [],
            'cmPhNumbers' => [],
        ];

        $this->assertSame($expected['program'], $this->target->program());
        $this->assertSame($expected['company_cm'], $this->target->company_cm());
        $this->assertSame($expected['product_cm'], $this->target->product_cm());
        $this->assertSame($expected['programDate'], $this->target->programDate());
        $this->assertSame($expected['cmDate'], $this->target->cmDate());
        $this->assertSame($expected['programPhNumbers'], $this->target->programPhNumbers());
        $this->assertSame($expected['cmPhNumbers'], $this->target->cmPhNumbers());
    }
}
