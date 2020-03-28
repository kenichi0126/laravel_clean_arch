<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialAdvertising\Get;

use App\Http\UserInterfaces\CommercialAdvertising\Get\CsvPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputData;
use Tests\TestCase;

class CsvPresenterTest extends TestCase
{
    private $presenterOutput;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->presenterOutput = $this->prophesize(PresenterOutput::class);

        $this->target = new CsvPresenter($this->presenterOutput->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $expected = "\n05\n06\n07\n08\n09\n10\n11\n12\n13\n14\n15\n16\n17\n18\n19\n20\n21\n22\n23\n24\n25\n26\n27\n28\n";

        ob_start();
        $output = new OutputData([], [], '0', 1, [], '20190101', '20190107', ['header' => []]);

        $this->target->__invoke($output);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertSame($expected, $actual);
    }
}
