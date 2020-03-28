<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Delete;

use App\Http\UserInterfaces\SearchConditions\Delete\Presenter;
use Prophecy\Argument as arg;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SearchConditions\Delete\UseCases\OutputData;
use Tests\TestCase;

/**
 * Class PresenterTest.
 */
final class PresenterTest extends TestCase
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
    public function invoke(): void
    {
        $this->presenterOutput->set(arg::cetera())->shouldBeCalled();

        $this->target->__invoke(new OutputData());
    }
}
