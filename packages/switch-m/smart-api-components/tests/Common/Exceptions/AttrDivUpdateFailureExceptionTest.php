<?php

namespace Switchm\SmartApi\Components\Tests\Common\Exceptions;

use Switchm\SmartApi\Components\Common\Exceptions\AttrDivUpdateFailureException;
use Switchm\SmartApi\Components\Tests\TestCase;

class AttrDivUpdateFailureExceptionTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @throws AttrDivUpdateFailureException
     */
    public function message(): void
    {
        $expected = '条件を更新できませんでした。他のユーザーによって削除された可能性があります。';

        $this->expectException(AttrDivUpdateFailureException::class);
        $this->expectExceptionMessage($expected);

        $this->target = new AttrDivUpdateFailureException();
        throw $this->target;
    }
}
