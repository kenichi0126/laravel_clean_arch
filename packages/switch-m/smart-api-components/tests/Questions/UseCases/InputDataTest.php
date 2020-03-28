<?php

namespace Switchm\SmartApi\Components\Tests\Questions\UseCases;

use Switchm\SmartApi\Components\Questions\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            '車',
            '全選択',
            '全選択'
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'keyword' => '車',
            'qGroup' => '全選択',
            'tag' => '全選択',
        ];

        $this->assertSame($expected['keyword'], $this->target->keyWord());
        $this->assertSame($expected['qGroup'], $this->target->qGroup());
        $this->assertSame($expected['tag'], $this->target->tag());
    }
}
