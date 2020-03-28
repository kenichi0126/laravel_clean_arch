<?php

namespace Switchm\SmartApi\Queries\Tests\Dao\Rdb;

use Mockery;
use Switchm\SmartApi\Queries\Dao\Rdb\MemberOriginalDivDao;
use Switchm\SmartApi\Queries\Tests\TestCase;

class MemberOriginalDivDaoTest extends TestCase
{
    /**
     * @var Mockery\Mock
     */
    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->target = Mockery::mock(MemberOriginalDivDao::class, [])->makePartial();
    }

    /**
     * @test
     */
    public function selectWithMenu(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->selectWithMenu('1', 'menu', '1');

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function selectDivisions(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->selectDivisions('1', ['ga8'], 1);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function selectCodes(): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->selectCodes(['ga8']);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     * @dataProvider selectDefinitionTextDataProvider
     * @param $defs
     */
    public function selectDefinitionText($defs): void
    {
        $expected = [];

        $this->target
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('select')
            ->andReturn($expected)
            ->once();

        $actual = $this->target->selectDefinitionText($defs);

        $this->assertEquals($expected, $actual);
    }

    public function selectDefinitionTextDataProvider(): array
    {
        return [
            [[]],
            [
                ['test=1,2,3', 'paneler_id=1'],
            ],
            [
                ['test=1-3'],
            ],
            [
                ['test=-3'],
            ],
            [
                ['test=1-'],
            ],
            [
                ['test=1'],
            ],
        ];
    }
}
