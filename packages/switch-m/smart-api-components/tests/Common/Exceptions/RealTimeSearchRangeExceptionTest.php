<?php

namespace Switchm\SmartApi\Components\Tests\Common\Exceptions;

use Switchm\SmartApi\Components\Common\Exceptions\RealtimeSearchRangeException;
use Switchm\SmartApi\Components\Tests\TestCase;

class RealtimeSearchRangeExceptionTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @throws RealtimeSearchRangeException
     */
    public function message(): void
    {
        $expected = '期間は100以降で指定してください。';

        $this->expectException(RealtimeSearchRangeException::class);
        $this->expectExceptionMessage($expected);

        $this->target = new RealtimeSearchRangeException(100);
        throw $this->target;
    }
}
