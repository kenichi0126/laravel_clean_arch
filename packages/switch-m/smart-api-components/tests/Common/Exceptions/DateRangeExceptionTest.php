<?php

namespace Switchm\SmartApi\Components\Tests\Common\Exceptions;

use Switchm\SmartApi\Components\Common\Exceptions\DateRangeException;
use Switchm\SmartApi\Components\Tests\TestCase;

class DateRangeExceptionTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @throws DateRangeException
     */
    public function message(): void
    {
        $expected = '期間は100日以内で指定してください。';

        $this->expectException(DateRangeException::class);
        $this->expectExceptionMessage($expected);

        $this->target = new DateRangeException(100);
        throw $this->target;
    }
}
