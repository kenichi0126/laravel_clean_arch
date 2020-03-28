<?php

namespace Tests\Unit\App\Http\UserInterfaces\RankingCommercial\Get;

use App\Http\UserInterfaces\RankingCommercial\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\RankingCommercial\Get\UseCases\OutputData;
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
        $output = new OutputData(['list'], 1, 100, '20200101', '20200107', ['header']);

        $this->presenterOutput
            ->set([
                'data' => $output->list(),
                'draw' => $output->draw(),
                'recordsFiltered' => $output->cnt(),
                'recordsTotal' => $output->cnt(),
//                'header' => $output->header()
            ])
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }
}
