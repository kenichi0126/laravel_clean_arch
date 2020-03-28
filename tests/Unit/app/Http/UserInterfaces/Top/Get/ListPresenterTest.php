<?php

namespace Tests\Unit\App\Http\UserInterfaces\Top\Get;

use App\Http\UserInterfaces\Top\Get\ListPresenter;
use Prophecy\Argument as arg;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Top\Get\UseCases\OutputData;
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
        $output = new OutputData('date', ['programs'], ['charts'], ['categories'], ['phNumbers']);

        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }

    /**
     * @test
     */
    public function invoke_abort(): void
    {
        $output = new OutputData('', [], [], [], []);

        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldNotBeCalled();

        $this->expectException(NotFoundHttpException::class);

        $this->target->__invoke($output);
    }
}
