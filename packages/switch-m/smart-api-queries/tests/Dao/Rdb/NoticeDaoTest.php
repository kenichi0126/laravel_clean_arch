<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\NoticeDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class NoticeDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(NoticeDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function searchSystemNotice(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->searchSystemNotice(1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function searchUserNotice(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->searchUserNotice(1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function searchSystemNoticeRead(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->searchSystemNoticeRead(1, 2);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function searchUserNoticeRead(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->searchUserNoticeRead(1, 2);

        $this->assertEquals($expected, $actual);
    }
}
