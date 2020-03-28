<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Get;

use App\Http\UserInterfaces\SettingAttrDivs\Get\Presenter;
use Prophecy\Argument as arg;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SettingAttrDivs\Get\UseCases\OutputData;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class PresenterTest extends TestCase
{
    private $presenterOutput;

    private $target;

    public function setUp(): void
    {
        parent::setUp();

        $this->presenterOutput = $this->prophesize(PresenterOutput::class);
        $this->target = new Presenter($this->presenterOutput->reveal());
    }

    /**
     * @test
     */
    public function invoke_failure(): void
    {
        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldNotBeCalled();

        $this->expectException(NotFoundHttpException::class);
        $this->target->__invoke(new OutputData([]));
    }

    /**
     * @test
     */
    public function invoke_success(): void
    {
        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke(new OutputData([[]]));
    }
}
