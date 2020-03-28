<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Delete;

use App\Http\UserInterfaces\SettingAttrDivs\Delete\Presenter;
use Prophecy\Argument as arg;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SettingAttrDivs\Delete\UseCases\OutputData;
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
    public function invoke_success(): void
    {
        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke(new OutputData());
    }
}
