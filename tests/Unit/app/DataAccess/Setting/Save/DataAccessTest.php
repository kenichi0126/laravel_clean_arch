<?php

namespace Tests\Unit\App\DataAccess\Setting\Save;

use App\DataAccess\Setting\Save\DataAccess;
use App\DataProxy\MemberSystemSettings;
use Tests\TestCase;

class DataAccessTest extends TestCase
{
    private $target;

    private $memberSystemSettings;

    public function setUp(): void
    {
        parent::setUp();

        $this->memberSystemSettings = $this->prophesize(MemberSystemSettings::class);

        $this->target = new DataAccess($this->memberSystemSettings->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $memberId = 1;
        $attribute = [
            'conv_15_sec_flag' => '1',
            'aggregate_setting' => 'ga12',
            'aggregate_setting_code' => json_encode([
                'division' => 'personal',
                'code' => 'personal',
                'division_name' => '個人',
                'name' => '個人',
            ]),
            'aggregate_setting_region_id' => 1,
        ];
        $this->memberSystemSettings->saveByMemberId($memberId, $attribute)->shouldBeCalled();
        $this->target->__invoke($memberId, $attribute);
    }
}
