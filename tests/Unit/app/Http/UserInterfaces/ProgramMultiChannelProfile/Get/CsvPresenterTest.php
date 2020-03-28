<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramMultiChannelProfile\Get;

use App\Http\UserInterfaces\ProgramMultiChannelProfile\Get\CsvPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramMultiChannelProfile\Get\UseCases\OutputData;
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
        $output = new OutputData(['data' => []], '20200101', '20200107', ['header' => []]);

        $expected = null;

        $actual = $this->target->__invoke($output);

        $this->assertSame($expected, $actual);
    }
}
