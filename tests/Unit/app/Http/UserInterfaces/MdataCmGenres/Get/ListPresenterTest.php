<?php

namespace Tests\Unit\App\Http\UserInterfaces\MdataCmGenres\Get;

use App\Http\UserInterfaces\MdataCmGenres\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\MdataCmGenres\Get\UseCases\OutputData;
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
        $output = new OutputData(['data']);

        $this->presenterOutput
            ->set($output->data())
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }

    /**
     * @test
     */
    public function invoke_abort(): void
    {
        $output = new OutputData([]);

        $this->presenterOutput
            ->set($output->data())
            ->shouldNotBeCalled();

        $this->expectException(NotFoundHttpException::class);

        $this->target->__invoke($output);
    }
}
