<?php

namespace Switchm\SmartApi\Components\Tests\Common\Exceptions;

use Switchm\SmartApi\Components\Common\Exceptions\RafCsvProductAxisException;
use Switchm\SmartApi\Components\Tests\TestCase;

class RafCsvProductAxisExceptionTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @throws RafCsvProductAxisException
     */
    public function message(): void
    {
        $expected = '集計軸に商品別を指定する場合、商品の数が100以内になるように絞り込みをしてください。';

        $this->expectException(RafCsvProductAxisException::class);
        $this->expectExceptionMessage($expected);

        $this->target = new RafCsvProductAxisException(100);
        throw $this->target;
    }
}
