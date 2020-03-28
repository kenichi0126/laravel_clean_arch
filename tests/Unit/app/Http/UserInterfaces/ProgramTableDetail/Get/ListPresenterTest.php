<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramTableDetail\Get;

use App\Http\UserInterfaces\ProgramTableDetail\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramTableDetail\Get\UseCases\OutputData;
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
        $output = new OutputData(['data'], ['headlines']);

        $this->presenterOutput
            ->set(['data' => $output->data(), 'headlines' => $output->headlines()])
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }
}
