<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataProgGenreDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class MdataProgGenreDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(MdataProgGenreDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function search(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->search();

        $this->assertEquals($expected, $actual);
    }
}
