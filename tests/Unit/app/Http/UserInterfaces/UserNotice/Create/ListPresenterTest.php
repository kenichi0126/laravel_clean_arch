<?php

namespace Tests\Unit\App\Http\UserInterfaces\UserNotice\Create;

use App\Http\UserInterfaces\UserNotice\Create\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\UserNotice\Create\UseCases\OutputData;
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
        $output = new OutputData();

        $this->presenterOutput
            ->set(response(null, 204))
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }
}
