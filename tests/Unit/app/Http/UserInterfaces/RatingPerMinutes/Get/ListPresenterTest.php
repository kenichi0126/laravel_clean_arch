<?php

namespace Tests\Unit\App\Http\UserInterfaces\RatingPerMinutes\Get;

use App\Http\UserInterfaces\RatingPerMinutes\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RatingPerMinutes\Get\UseCases\OutputData;
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
        $output = new OutputData([], [], [], [], [], [], [], [], [], [], []);

        $this->presenterOutput
            ->set([
                'data' => $output->data(),
                'draw' => $output->draw(),
                'recordsFiltered' => $output->recordsFiltered(),
                'recordsTotal' => $output->recordsTotal(),
                'dateList' => $output->dateList(), ])
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }
}
