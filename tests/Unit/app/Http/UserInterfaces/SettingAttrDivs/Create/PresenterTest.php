<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Create;

use App\Http\UserInterfaces\SettingAttrDivs\Create\Presenter;
use Prophecy\Argument as arg;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Common\Exceptions\AttrDivCreationLimitOverException;
use Switchm\SmartApi\Components\SettingAttrDivs\Create\UseCases\OutputData;
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
     * @throws AttrDivCreationLimitOverException
     */
    public function invoke_failure(): void
    {
        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldNotBeCalled();

        $this->expectException(AttrDivCreationLimitOverException::class);

        $this->target->__invoke(new OutputData(false));
    }

    /**
     * @test
     */
    public function invoke_success(): void
    {
        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke(new OutputData(true));
    }
}
