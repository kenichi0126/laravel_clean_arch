<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Dwh;

use Carbon\Carbon;
use Mockery;
use Switchm\SmartApi\Queries\Dao\Dwh\TopDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class TopDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(TopDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function findProgramRanking(): void
    {
        $from = new Carbon('2019-06-14');
        $to = new Carbon('2019-06-15');
        $channelIds = [1, 2, 3, 4, 5];
        $expectBinds = [
            ':from' => $from,
            ':to' => $to,
            ':channelIds0' => 1,
            ':channelIds1' => 2,
            ':channelIds2' => 3,
            ':channelIds3' => 4,
            ':channelIds4' => 5,
        ];
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'select'))
            ->once()->ordered();

        $actual = $this->target->findProgramRanking($from, $to, $channelIds);
    }

    /**
     * @test
     */
    public function findCmRankingOfCompany(): void
    {
        $from = new Carbon('2019-06-14');
        $to = new Carbon('2019-06-15');
        $regionId = 1;
        $channelIds = [1, 2, 3, 4, 5];
        $conv15SecFlag = 0;
        $broadcasterCompanyIds = [10, 11];
        $expectBinds = [
            ':from' => $from,
            ':to' => $to,
            ':regionId' => $regionId,
            ':channelIds0' => 1,
            ':channelIds1' => 2,
            ':channelIds2' => 3,
            ':channelIds3' => 4,
            ':channelIds4' => 5,
            ':exclusionCompanyIds0' => 10,
            ':exclusionCompanyIds1' => 11,
        ];
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'select'))
            ->once()->ordered();

        $actual = $this->target->findCmRankingOfCompany($from, $to, $regionId, $channelIds, $conv15SecFlag, $broadcasterCompanyIds);
    }

    /**
     * @test
     */
    public function findCmRankingOfProduct(): void
    {
        $from = new Carbon('2019-06-14');
        $to = new Carbon('2019-06-15');
        $regionId = 1;
        $channelIds = [1, 2, 3, 4, 5];
        $broadcasterCompanyIds = [10, 11];
        $expectBinds = [
            ':from' => $from,
            ':to' => $to,
            ':regionId' => $regionId,
            ':channelIds0' => 1,
            ':channelIds1' => 2,
            ':channelIds2' => 3,
            ':channelIds3' => 4,
            ':channelIds4' => 5,
            ':exclusionCompanyIds0' => 10,
            ':exclusionCompanyIds1' => 11,
        ];
        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->withArgs($this->bindAsserts($expectBinds, 'select'))
            ->once()->ordered();

        $actual = $this->target->findCmRankingOfProduct($from, $to, $regionId, $channelIds, $broadcasterCompanyIds);
    }

    private function bindAsserts($bindings, $caller)
    {
        return function ($query, $binds) use ($bindings, $caller) {
            $this->assertCount(count($bindings), $binds, substr($query, 0, 100) . $caller . print_r($binds, true));

            foreach ($bindings as $key => $val) {
                $this->assertEquals($val, $binds[$key], "${caller} ${binds}" . print_r($binds, true));
            }
            return true;
        };
    }
}
