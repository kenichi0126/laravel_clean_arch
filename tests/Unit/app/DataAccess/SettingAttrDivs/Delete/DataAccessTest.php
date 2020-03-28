<?php

namespace Tests\Unit\App\DataAccess\SettingAttrDivs\Delete;

use App\DataAccess\SettingAttrDivs\Delete\DataAccess;
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
        $this->attrDivs->deleteByDivisionAndCode($division, $code)->shouldBeCalled();
        $this->target->__invoke($division, $code);
    }
}
