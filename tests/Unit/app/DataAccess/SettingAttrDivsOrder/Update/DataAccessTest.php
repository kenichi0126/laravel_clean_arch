<?php

namespace Tests\Unit\App\DataAccess\SettingAttrDivsOrder\Update;

use App\DataAccess\SettingAttrDivsOrder\Update\DataAccess;
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
        $divisions = [
            '' => [
                '',
            ],
        ];
        $division = '';
        $code = '';
        $attributes = ['display_order' => 1];

        $this->attrDivs->updateByDivisionAndCode($division, $code, $attributes)->shouldBeCalled();
        $this->target->__invoke($divisions);
    }
}
