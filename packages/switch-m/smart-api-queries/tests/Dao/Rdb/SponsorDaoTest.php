<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\SponsorDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class SponsorDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(SponsorDao::class, [])->makePartial();
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @param mixed $record
     * @param mixed $expected
     */
    public function sponsorBasic($record, $expected): void
    {
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('selectOne')
            ->andReturn($record)
            ->once();

        $actual = $this->target->sponsorBasic(1);

        $this->assertEquals($expected, $actual);
    }

    public function dataProvider()
    {
        return [
            [null, null],
            [(object) ['permissions' => true], (object) ['permissions' => true]],
            [(object) ['trial_settings' => true], (object) ['trial_settings' => true]],
        ];
    }
}
