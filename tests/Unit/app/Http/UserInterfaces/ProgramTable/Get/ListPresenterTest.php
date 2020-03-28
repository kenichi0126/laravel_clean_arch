<?php

namespace Tests\Unit\App\Http\UserInterfaces\ProgramTable\Get;

use App\Http\UserInterfaces\ProgramTable\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\ProgramTable\Get\UseCases\OutputData;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
        $output = new OutputData(['list'], 1, ['dateList'], []);

        $this->presenterOutput
            ->set([
                'data' => $output->data(),
                'draw' => $output->draw(),
                'dateList' => $output->dateList(),
                'header' => $output->header(),
            ])
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }

    /**
     * @test
     */
    public function invoke_exception(): void
    {
        $output = new OutputData([], 1, ['dateList'], ['header']);

        $this->presenterOutput
            ->set([
                'data' => $output->data(),
                'draw' => $output->draw(),
                'dateList' => $output->dateList(),
                'header' => $output->header(),
            ])
            ->shouldNotBeCalled();

        $this->expectException(NotFoundHttpException::class);

        $this->target->__invoke($output);
    }
}
