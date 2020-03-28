<?php

namespace Switchm\SmartApi\Components\Tests\RafChart\UseCases;

use Switchm\SmartApi\Components\RafChart\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class OutputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $params = [
            $series = ['series'],
            $categories = ['categories'],
            $average = ['average'],
            $overOne = ['overOne'],
            $grp = ['grp'],
            $csvButtonInfo = ['csvButtonInfo'],
            $header = ['header'],
        ];

        $this->target = new OutputData(...$params);
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'series' => ['series'],
            'categories' => ['categories'],
            'average' => ['average'],
            'overOne' => ['overOne'],
            'grp' => ['grp'],
            'csvButtonInfo' => ['csvButtonInfo'],
            'header' => ['header'],
        ];
        $this->assertSame($expected['series'], $this->target->series());
        $this->assertSame($expected['categories'], $this->target->categories());
        $this->assertSame($expected['average'], $this->target->average());
        $this->assertSame($expected['overOne'], $this->target->overOne());
        $this->assertSame($expected['grp'], $this->target->grp());
        $this->assertSame($expected['csvButtonInfo'], $this->target->csvButtonInfo());
        $this->assertSame($expected['header'], $this->target->header());
    }
}
