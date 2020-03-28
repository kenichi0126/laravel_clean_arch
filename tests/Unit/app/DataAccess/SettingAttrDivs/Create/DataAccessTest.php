<?php

namespace Tests\Unit\App\DataAccess\SettingAttrDivs\Create;

use App\DataAccess\SettingAttrDivs\Create\DataAccess;
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
        $attribute = [];
        $this->attrDivs->create($attribute)->shouldBeCalled();
        $this->target->__invoke($attribute);
    }
}
