<?php

namespace Switchm\SmartApi\Components\Tests\Common;

use Switchm\SmartApi\Components\Common\ValidatorSetterTrait;
use Switchm\SmartApi\Components\Tests\TestCase;

class ValidatorSetterTraitTest extends TestCase
{
    use ValidatorSetterTrait;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function input($key)
    {
        return $key;
    }

    /**
     * @test
     */
    public function SearchableNumberOfDaysValidatorFieldTest(): void
    {
        $expected = [
            'SearchableNumberOfDaysValidator' => [
                'division' => 'division',
                'requestPeriod' => 'dateRange',
            ],
        ];

        $actual = $this->SearchableNumberOfDaysValidatorField();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function SearchableBoundaryValidatorFieldTest(): void
    {
        $expected = [
            'searchableBoundaryValidator' => [
                'startDateTime' => 'startDateTime',
                'endDateTime' => 'endDateTime',
                'dataType' => 'dataType',
                'regionId' => 'regionId',
            ],
        ];

        $actual = $this->SearchableBoundaryValidatorField();
        $this->assertEquals($expected, $actual);
    }
}
