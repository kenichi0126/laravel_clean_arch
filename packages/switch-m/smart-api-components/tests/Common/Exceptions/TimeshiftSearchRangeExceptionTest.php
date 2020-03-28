<?php

namespace Switchm\SmartApi\Components\Tests\Common\Exceptions;

use Switchm\SmartApi\Components\Common\Exceptions\TimeshiftSearchRangeException;
use Switchm\SmartApi\Components\Tests\TestCase;

class TimeshiftSearchRangeExceptionTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @throws TimeshiftSearchRangeException
     */
    public function message(): void
    {
        $expected = '期間は100～放送日より7日前以上開けて指定してください。※タイムシフト／総合視聴率を含む場合';

        $this->expectException(TimeshiftSearchRangeException::class);
        $this->expectExceptionMessage($expected);

        $this->target = new TimeshiftSearchRangeException(100);
        throw $this->target;
    }
}
