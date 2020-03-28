<?php

namespace Tests\Unit\App\DataAccess\SettingAttrDivs\Update;

use App\DataAccess\SettingAttrDivs\Update\DataAccess;
use App\DataProxy\AttrDivs;
use Tests\TestCase;

class DataAccessTest extends TestCase
{
    private $target;

    private $attrDivs;

    public function setUp(): void
    {
        parent::setUp();

        $this->attrDivs = $this->prophesize(AttrDivs::class);

        $this->target = new DataAccess($this->attrDivs->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $division = '';
        $code = '';
        $attributes = [];
        $this->attrDivs->updateByDivisionAndCode($division, $code, $attributes)->shouldBeCalled();
        $this->target->__invoke($division, $code, $attributes);
    }
}
