<?php

namespace Tests\Unit\App\Http\UserInterfaces\RatingPerHourly\Get;

use App\Http\UserInterfaces\RatingPerHourly\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RatingPerHourly\Get\UseCases\OutputData;
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
        $output = new OutputData(
            ['data'],
            1,
            100,
            [],
            [],
            '',
            '',
            '20200101',
            '20200107',
            ['dateList']
        );

        $this->presenterOutput
            ->set([
                'data' => $output->data(),
                'draw' => $output->draw(),
                'recordsFiltered' => $output->cnt(),
                'recordsTotal' => $output->cnt(),
                'dateList' => $output->dateList(),
            ])
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }
}
