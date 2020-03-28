<?php

namespace Switchm\SmartApi\Components\Tests\Common\Exceptions;

use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\Tests\TestCase;

class SampleCountExceptionTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @throws SampleCountException
     */
    public function message(): void
    {
        $expected = '指定条件では、該当サンプル数が100に達していません。条件を見直してください。';

        $this->expectException(SampleCountException::class);
        $this->expectExceptionMessage($expected);

        $this->target = new SampleCountException(100);
        throw $this->target;
    }
}
