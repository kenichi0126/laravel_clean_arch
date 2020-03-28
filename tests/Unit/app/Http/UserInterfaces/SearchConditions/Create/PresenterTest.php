<?php

namespace Tests\Unit\App\Http\UserInterfaces\SearchConditions\Create;

use App\Http\UserInterfaces\SearchConditions\Create\Presenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\SearchConditions\Create\UseCases\OutputData;
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
        $this->presenterOutput->set(response(null, 204))->shouldBeCalled();

        $this->target->__invoke(new OutputData(true));
    }

    /**
     * @test
     */
    public function invoke_upper_error(): void
    {
        $this->presenterOutput->set(response($this->target::UPPER_LIMIT_ERROR, 400))->shouldBeCalled();

        $this->target->__invoke(new OutputData(false));
    }
}
