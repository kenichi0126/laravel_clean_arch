<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\UserInfoDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class UserInfoDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(UserInfoDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function getHoliday(): void
    {
        $expected = new \stdClass();

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getUserInfo(1);

        $this->assertEquals($expected, $actual);
    }
}
