<?php

namespace Switchm\SmartApi\Components\Tests\Common\Exceptions;

use Switchm\SmartApi\Components\Common\Exceptions\TrialException;
use Switchm\SmartApi\Components\Tests\TestCase;

class TrialExceptionTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     * @throws TrialException
     */
    public function message(): void
    {
        $expected = 'トライアル期間は 2019-01-01 - 2019-01-07 の期間のみ検索可能です。';

        $this->expectException(TrialException::class);
        $this->expectExceptionMessage($expected);

        $user = (object) ['sponsor' => (object) ['sponsorTrial' => (object) ['settings' => ['search_range' => ['start' => '20190101', 'end' => '20190107']]]]];

        $this->target = new TrialException($user);
        throw $this->target;
    }
}
