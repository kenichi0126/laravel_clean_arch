<?php

namespace Tests\Unit\App\Http\UserInterfaces\SampleCount\Get;

use App\Http\UserInterfaces\SampleCount\Get\ListPresenter;
use Prophecy\Argument as arg;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\Common\Exceptions\SampleCountException;
use Switchm\SmartApi\Components\SampleCount\Get\UseCases\OutputData;
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
     * @throws SampleCountException
     */
    public function invoke_sponsor1(): void
    {
        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke(new OutputData(['cnt' => 1000], true));
    }

    /**
     * @test
     * @dataProvider invoke_cnt_editFlg_dataProvider
     * @param $spnser_id
     * @param $cnt
     * @param $editFlg
     * @throws SampleCountException
     */
    public function invoke($cnt, $editFlg): void
    {
        $this->presenterOutput
            ->set(arg::cetera())
            ->shouldBeCalled();

        $this->target->__invoke(new OutputData(['cnt' => $cnt], $editFlg));
    }

    public function invoke_cnt_editFlg_dataProvider()
    {
        return [
            [
                1000,
                true,
            ],
            [
                10,
                true,
            ],
            [
                1000,
                false,
            ],
        ];
    }
}
