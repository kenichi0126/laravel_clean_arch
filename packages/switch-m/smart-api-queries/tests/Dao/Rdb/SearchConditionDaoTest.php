<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use stdClass;
use Switchm\SmartApi\Queries\Dao\Rdb\SearchConditionDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

/**
 * Class SearchConditionDaoTest.
 */
final class SearchConditionDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(SearchConditionDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function findByMemberId(): void
    {
        $expected = [];
        $expectedBinds = [
            ':regionId' => 1,
            ':memberId' => 2,
        ];

        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('select')->with(Mockery::any(), $expectedBinds)->andReturn($expected)->once();

        $actual = $this->target->findByMemberId(1, 2, 'name', 'desc');

        $this->assertSame($actual, $expected);
    }

    /**
     * @test
     */
    public function countByMemberId(): void
    {
        $expected = 1;
        $expectedBinds = [
            ':regionId' => 1,
            ':memberId' => 2,
        ];

        $returnObj = new stdClass();
        $returnObj->count = $expected;

        $this->target->shouldAllowMockingProtectedMethods()->shouldReceive('selectOne')->with(Mockery::any(), $expectedBinds)->andReturn($returnObj)->once();

        $actual = $this->target->countByMemberId(1, 2);

        $this->assertSame($actual, $expected);
    }
}
