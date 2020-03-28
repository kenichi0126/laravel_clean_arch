<?php

namespace Tests\Unit\App\Http\UserInterfaces\RafChart;

use App\Http\UserInterfaces\RafChart\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RafChart\Get\UseCases\OutputData;
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
        $params = [
            $series = [],
            $categories = [],
            $average = [],
            $overOne = [],
            $grp = [],
            $csvButtonInfo = [],
            $header = [],
        ];

        $output = new OutputData(...$params);

        $this->presenterOutput->set(
            [
                    'series' => $output->series(),
                    'categories' => $output->categories(),
                    'tableData' => [
                        'average' => $output->average(),
                        'overOne' => $output->overOne(),
                        'grp' => $output->grp(),
                    ],
                    'csvButtonInfo' => $output->csvButtonInfo(),
                ]
        )
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }
}
