<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialList\Get;

use App\Http\UserInterfaces\CommercialList\Get\CsvPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialList\Get\UseCases\OutputData;
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
        $expected = "\n\n";

        $output = new OutputData(['list' => []], 1, 100, '20200101', '20200107', ['header' => []]);

        ob_start();
        $this->target->__invoke($output);
        $actual = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($expected, $actual);
    }
}
