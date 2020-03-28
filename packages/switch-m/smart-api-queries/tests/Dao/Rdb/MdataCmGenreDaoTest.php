<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\MdataCmGenreDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class MdataCmGenreDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(MdataCmGenreDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function getHoliday(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->getCmLargeGenres();

        $this->assertEquals($expected, $actual);
    }
}
