<?php

namespace Tests\Unit\App\Http\UserInterfaces\SettingAttrDivs\Update;

use App\Http\UserInterfaces\SettingAttrDivs\Update\Presenter;
use Prophecy\Argument as arg;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Common\Exceptions\AttrDivUpdateFailureException;
use Switchm\SmartApi\Components\SettingAttrDivs\Update\UseCases\OutputData;
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
     * @throws AttrDivUpdateFailureException
     */
    public function invoke_failure(): void
    {
        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldNotBeCalled();

        $this->expectException(AttrDivUpdateFailureException::class);

        $this->target->__invoke(new OutputData(0));
    }

    /**
     * @test
     */
    public function invoke_success(): void
    {
        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke(new OutputData(1));
    }
}
