<?php

namespace Switchm\SmartApi\Components\Tests\Divisions\Get\UseCases;

use Prophecy\Argument as arg;
use Switchm\SmartApi\Components\Divisions\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Divisions\Get\UseCases\Interactor;
use Switchm\SmartApi\Components\Divisions\Get\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\Divisions\Get\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\DivisionDao;
use Switchm\SmartApi\Queries\Dao\Rdb\MemberOriginalDivDao;

class InteractorTest extends TestCase
{
    private $divisionDao;

    private $memberOriginalDivDao;

    private $outputBoundary;

    private $target;

    /**
     * @test
     */
    public function __invoke_data_not_exist(): void
    {
        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->divisionDao
            ->findOriginalDiv(arg::cetera())
            ->willReturn([])
            ->shouldNotBeCalled();

        $this->divisionDao
            ->find(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $this->divisionDao
            ->getPersonalHouseHold(arg::cetera())
            ->willReturn(['household'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData([], []))
            ->willReturn()
            ->shouldNotBeCalled();

        $userInfo = new \stdClass();
        $userInfo->id = 1;

        $inputData = new InputData('menu', 1, $userInfo, false);

        $this->target->__invoke($inputData);
    }

    /**
     * @test
     */
    public function __invoke_data_exists(): void
    {
        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn(['ga8'])
            ->shouldBeCalled();

        $this->divisionDao
            ->findOriginalDiv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $menu = new \stdClass();
        $menu->division = 'ga8';
        $this->divisionDao
            ->find(arg::cetera())
            ->willReturn([$menu])
            ->shouldBeCalled();

        $this->divisionDao
            ->getPersonalHouseHold(arg::cetera())
            ->willReturn(['household'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(
                ['ga8' => ['division' => 'ga8']],
                ['ga8' => ['household', ['division' => 'ga8'], null]]
            ))
            ->willReturn()
            ->shouldBeCalled();

        $userInfo = new \stdClass();
        $userInfo->id = 1;

        $inputData = new InputData('menu', 1, $userInfo, false);

        $this->target->__invoke($inputData);
    }

    /**
     * @test
     */
    public function __invoke_cross_condition_permission_true(): void
    {
        $this->memberOriginalDivDao
            ->selectWithMenu(arg::cetera())
            ->willReturn(['ga8'])
            ->shouldBeCalled();

        $this->divisionDao
            ->findOriginalDiv(arg::cetera())
            ->willReturn([])
            ->shouldBeCalled();

        $menu = new \stdClass();
        $menu->division = 'ga8';
        $this->divisionDao
            ->find(arg::cetera())
            ->willReturn([$menu])
            ->shouldBeCalled();

        $this->divisionDao
            ->getPersonalHouseHold(arg::cetera())
            ->willReturn(['household'])
            ->shouldBeCalled();

        $this->outputBoundary
            ->__invoke(new OutputData(
                ['ga8' => ['division' => 'ga8'],
                    'condition_cross' => [
                            'code' => 'condition_cross',
                            'division' => 'condition_cross',
                            'division_name' => '掛け合わせ条件',
                            'name' => '',
                        ], ],
                ['ga8' => ['household', ['division' => 'ga8'], null]]
            ))
            ->willReturn()
            ->shouldBeCalled();

        $userInfo = new \stdClass();
        $userInfo->id = 1;

        $inputData = new InputData('menu', 1, $userInfo, true);

        $this->target->__invoke($inputData);
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->divisionDao = $this->prophesize(DivisionDao::class);
        $this->memberOriginalDivDao = $this->prophesize(MemberOriginalDivDao::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->divisionDao->reveal(),
            $this->memberOriginalDivDao->reveal(),
            $this->outputBoundary->reveal()
        );
    }
}
