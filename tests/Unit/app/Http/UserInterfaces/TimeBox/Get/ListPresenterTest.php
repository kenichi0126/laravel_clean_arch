<?php

namespace Tests\Unit\App\Http\UserInterfaces\TimeBox\Get;

use App\Http\UserInterfaces\TimeBox\Get\ListPresenter;
use Carbon\Carbon;
use Prophecy\Argument as arg;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\TimeBox\Get\UseCases\OutputData;
use Tests\TestCase;

class ListPresenterTest extends TestCase
{
    private $presenterOutput;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->presenterOutput = $this->prophesize(PresenterOutput::class);

        $this->target = new ListPresenter($this->presenterOutput->reveal());
    }

    /**
     * @test
     */
    public function invoke(): void
    {
        $output = new OutputData(1, 2, Carbon::parse('2019-01-01 00:00:00'), 7, 1, Carbon::parse('2019-01-01 00:00:00'), Carbon::parse('2019-01-08 00:00:00'), 10, 20);

        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }
}
