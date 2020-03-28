<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Update\UseCases;

use Prophecy\Argument as arg;
use ReflectionClass;
use ReflectionException;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\InputData;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\Interactor;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\OutputData;
use Switchm\SmartApi\Components\Tests\TestCase;
use Switchm\SmartApi\Queries\Dao\Rdb\AttrDivDao;
use Switchm\SmartApi\Queries\Dao\Rdb\EnqDao;

class InteractorTest extends TestCase
{
    private $attrDivDao;

    private $enqDao;

    private $dataAccess;

    private $outputBoundary;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->attrDivDao = $this->prophesize(AttrDivDao::class);
        $this->enqDao = $this->prophesize(EnqDao::class);
        $this->dataAccess = $this->prophesize(DataAccessInterface::class);
        $this->outputBoundary = $this->prophesize(OutputBoundary::class);

        $this->target = new Interactor(
            $this->attrDivDao->reveal(),
            $this->enqDao->reveal(),
            $this->dataAccess->reveal(),
            $this->outputBoundary->reveal()
        );
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function createDefinition(): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('createDefinition');
        $method->setAccessible(true);

        $expected = 'gender=f,m:age=10-99:occupation=:married=';

        $actual = $method->invoke(
            $this->target,
            ['gender' => ['f', 'm'], 'age' => ['from' => 10, 'to' => 99], 'occupation' => [''], 'married' => [''], 'dispOccupation' => [''],
                'child' => [
                    'enabled' => false,
                ],
            ],
            [],
            1
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function createDefinition_childage_all(): void
    {
        $reflection = new ReflectionClass($this->target);
        $method = $reflection->getMethod('createDefinition');
        $method->setAccessible(true);

        $expected = 'gender=f,m:age=10-99:occupation=:married=:childage=3_8';

        $actual = $method->invoke(
            $this->target,
            ['gender' => ['f', 'm'], 'age' => ['from' => 10, 'to' => 99], 'occupation' => [''], 'married' => [''], 'dispOccupation' => [''],
                'child' => [
                    'enabled' => true,
                    'gender' => [''],
                    'age' => [
                        'from' => 3,
                        'to' => 8,
                    ],
                ],
            ],
            [],
            1
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function createDefinition_childage_f(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('createDefinition');
        $method->setAccessible(true);

        $expected = 'gender=f,m:age=10-99:occupation=:married=:childage_f=10_20';

        $actual = $method->invoke(
            $this->target,
            ['gender' => ['f', 'm'], 'age' => ['from' => 10, 'to' => 99], 'occupation' => [''], 'married' => [''], 'dispOccupation' => [''],
                'child' => [
                    'enabled' => true,
                    'gender' => ['f'],
                    'age' => [
                        'from' => 10,
                        'to' => 20,
                    ],
                ],
            ],
            [],
            1
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function createDefinition_childage_m(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('createDefinition');
        $method->setAccessible(true);

        $expected = 'gender=f,m:age=10-99:occupation=:married=:childage_m=5_15';

        $actual = $method->invoke(
            $this->target,
            ['gender' => ['f', 'm'], 'age' => ['from' => 10, 'to' => 99], 'occupation' => [''], 'married' => [''], 'dispOccupation' => [''],
                'child' => [
                    'enabled' => true,
                    'gender' => ['m'],
                    'age' => [
                        'from' => 5,
                        'to' => 15,
                    ],
                ],
            ],
            [],
            1
        );

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function createRestoreInfo(): void
    {
        $reflection = new \ReflectionClass($this->target);
        $method = $reflection->getMethod('createRestoreInfo');
        $method->setAccessible(true);

        $expected = ['info' => '', 'text' => ''];

        $actual = $method->invoke($this->target, []);

        $this->assertSame($expected, $actual);
    }

    /**
     * @test
     */
    public function invoke_info中身空っぽ(): void
    {
        $input = new InputData(
            '',
            [],
            [],
            1,
            '',
            ''
        );

        $outputData = new OutputData(1);

        $this->attrDivDao
            ->getAttrDiv(arg::cetera())
            ->willReturn(['list' => []])
            ->shouldBeCalled();

        $this->dataAccess
            ->__invoke(arg::cetera())
            ->willReturn(1)
            ->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_info中身1グループ(): void
    {
        $input = new InputData(
            '',
            [],
            [
                [
                    'connectorLinkingType' => '',
                    'innerLinkingType' => 'AND',
                    'key' => 0,
                    'values' => [
                        [
                            'name' => '持ち家・一戸建て',
                            'val' => 1,
                            'answer_column' => 'i0002',
                            'q_type' => 'SAR',
                            'a_type' => 'SA',
                            'index' => 1,
                            'key' => '居住形態',
                            'q_no' => 'i2',
                            'title' => '1-Q2.居住形態',
                        ],
                    ],
                ],
            ],
            1,
            '',
            ''
        );

        $outputData = new OutputData(1);

        $this->attrDivDao
            ->getAttrDiv(arg::cetera())
            ->willReturn(['list' => []])
            ->shouldBeCalled();

        $this->enqDao
            ->getPanelerIds(arg::cetera())
            ->willReturn(['list' => [1]])
            ->shouldBeCalled();

        $this->dataAccess
            ->__invoke(arg::cetera())
            ->willReturn(1)
            ->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_info中身2グループ(): void
    {
        $input = new InputData(
            '',
            [],
            [
                [
                    'connectorLinkingType' => 'AND',
                    'innerLinkingType' => 'OR',
                    'key' => 0,
                    'values' => [
                        [
                            'name' => '持ち家・一戸建て',
                            'val' => 1,
                            'answer_column' => 'i0002',
                            'q_type' => 'SAR',
                            'a_type' => 'SA',
                            'index' => 1,
                            'key' => '居住形態',
                            'q_no' => 'i2',
                            'title' => '1-Q2.居住形態',
                        ],
                    ],
                ],
                [
                    'connectorLinkingType' => '',
                    'innerLinkingType' => 'OR',
                    'key' => 0,
                    'values' => [
                        [
                            'name' => '三世代世帯（親と子と孫）',
                            'val' => 4,
                            'answer_column' => 'i0001',
                            'q_type' => 'SAR',
                            'a_type' => 'SA',
                            'index' => 4,
                            'key' => '家族構成',
                            'q_no' => 'i1',
                            'title' => '1-Q1.家族構成',
                        ],
                    ],
                ],
            ],
            1,
            '',
            ''
        );

        $outputData = new OutputData(1);

        $this->attrDivDao
            ->getAttrDiv(arg::cetera())
            ->willReturn(['list' => []])
            ->shouldBeCalled();

        $this->enqDao
            ->getPanelerIds(arg::cetera())
            ->willReturn(['list' => [1]])
            ->shouldBeCalled();

        $this->dataAccess
            ->__invoke(arg::cetera())
            ->willReturn(1)
            ->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_getAttrDiv中身空っぽ(): void
    {
        $input = new InputData(
            '',
            [],
            [],
            1,
            '',
            ''
        );

        $outputData = new OutputData(0);

        $this->attrDivDao
            ->getAttrDiv(arg::cetera())
            ->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }
}
