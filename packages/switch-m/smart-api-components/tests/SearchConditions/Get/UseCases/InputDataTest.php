<?php

namespace Switchm\SmartApi\Components\Tests\SearchConditions\Get\UseCases;

use Switchm\SmartApi\Components\SearchConditions\Get\UseCases\InputData;
use Switchm\SmartApi\Components\Tests\TestCase;

/**
 * Class InputDataTest.
 */
final class InputDataTest extends TestCase
{
    private $target;

    public function setUp(): void
    {
        parent::setUp();
        $this->target = new InputData(
            1,
            1,
            'name',
            'desc',
            true,
            true,
            true,
            true,
            true,
            true
        );
    }

    /**
     * @test
     */
    public function getterTest(): void
    {
        $expected = [
            'regionId' => 1,
            'memberId' => 1,
            'orderColumn' => 'name',
            'orderDirection' => 'desc',
            'permissionRankingCommercial' => true,
            'permissionTimeShifting' => true,
            'permissionBsInfo' => true,
            'permissionCmMaterials' => true,
            'permissionTimeSpot' => true,
            'permissionMultipleCondition' => true,
        ];

        $this->assertSame($expected['regionId'], $this->target->regionId());
        $this->assertSame($expected['memberId'], $this->target->memberId());
        $this->assertSame($expected['orderColumn'], $this->target->orderColumn());
        $this->assertSame($expected['orderDirection'], $this->target->orderDirection());
        $this->assertSame($expected['permissionRankingCommercial'], $this->target->permissionRankingCommercial());
        $this->assertSame($expected['permissionTimeShifting'], $this->target->permissionTimeShifting());
        $this->assertSame($expected['permissionBsInfo'], $this->target->permissionBsInfo());
        $this->assertSame($expected['permissionCmMaterials'], $this->target->permissionCmMaterials());
        $this->assertSame($expected['permissionTimeSpot'], $this->target->permissionTimeSpot());
        $this->assertSame($expected['permissionMultipleCondition'], $this->target->permissionMultipleCondition());
    }
}
