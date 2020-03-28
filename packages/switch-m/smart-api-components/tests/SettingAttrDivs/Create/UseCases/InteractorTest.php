<?php

namespace Switchm\SmartApi\Components\Tests\SettingAttrDivs\Create\UseCases;

use Prophecy\Argument as arg;
use ReflectionClass;
use ReflectionException;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\DataAccessInterface;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\InputData;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\Interactor;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\OutputBoundary;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\OutputData;
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
    public function invoke_中身空っぽ(): void
    {
        $input = new InputData(
            '',
            [],
            [],
            1,
            '',
            1
        );

        $outputData = new OutputData(true);

        $this->attrDivDao
            ->getDisplayOrder(arg::cetera())
            ->willReturn(['list' => [[]]])
            ->shouldBeCalled();

        $this->dataAccess->__invoke(arg::cetera())->shouldBeCalled();

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
            1
        );

        $outputData = new OutputData(true);

        $this->attrDivDao
            ->getDisplayOrder(arg::cetera())
            ->willReturn(['list' => [[]]])
            ->shouldBeCalled();

        $this->enqDao
            ->getPanelerIds(arg::cetera())
            ->willReturn(['list' => [1]])
            ->shouldBeCalled();

        $this->dataAccess->__invoke(arg::cetera())->shouldBeCalled();

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
            1
        );

        $outputData = new OutputData(true);

        $this->attrDivDao
            ->getDisplayOrder(arg::cetera())
            ->willReturn(['list' => [[]]])
            ->shouldBeCalled();

        $this->enqDao
            ->getPanelerIds(arg::cetera())
            ->willReturn(['list' => [1]])
            ->shouldBeCalled();

        $this->dataAccess->__invoke(arg::cetera())->shouldBeCalled();

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }

    /**
     * @test
     */
    public function invoke_既に10件以上登録されている場合(): void
    {
        $outputData = new OutputData(false);

        $this->attrDivDao
            ->getDisplayOrder(arg::cetera())
            ->willReturn(['list' => [[], [], [], [], [], [], [], [], [], []]])
            ->shouldBeCalled();

        $input = new InputData(
            '',
            [],
            [],
            1,
            '',
            1
        );

        $this->outputBoundary->__invoke($outputData)->shouldBeCalled();
        $this->target->__invoke($input);
    }
}
