<?php

namespace Switchm\SmartApi\Components\Tests\Common\Exceptions;

use Switchm\SmartApi\Components\Common\Exceptions\AttrDivCreationLimitOverException;
use Switchm\SmartApi\Components\Tests\TestCase;

class AttrDivCreationLimitOverExceptionTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @throws AttrDivCreationLimitOverException
     */
    public function message(): void
    {
        $expected = '条件を保存できる数の限界を超えています。他のユーザーによって追加された可能性があります。';

        $this->expectException(AttrDivCreationLimitOverException::class);
        $this->expectExceptionMessage($expected);

        $this->target = new AttrDivCreationLimitOverException();
        throw $this->target;
    }
}
