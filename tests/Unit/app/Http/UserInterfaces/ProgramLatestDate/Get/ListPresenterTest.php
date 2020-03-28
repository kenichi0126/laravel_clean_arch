<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramLatestDate\Get;

use App\Http\UserInterfaces\ProgramLatestDate\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramLatestDate\Get\UseCases\OutputData;
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
        $output = new OutputData(['data']);

        $this->presenterOutput
            ->set($output->data())
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }
}
