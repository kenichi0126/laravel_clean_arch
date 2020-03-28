<?php

namespace Tests\Unit\App\Http\UserInterfaces\CommercialAdvertising\Get;

use App\Http\UserInterfaces\CommercialAdvertising\Get\ListPresenter;
use Switchm\Php\Illuminate\Http\Middleware\PresenterOutput;
use Switchm\SmartApi\Components\CommercialAdvertising\Get\UseCases\OutputData;
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
        $output = new OutputData([], [], '0', 1, [], '20190101', '20190107', ['header']);

        $this->presenterOutput
            ->set([
                'data' => [
                    ['hour' => '05'],
                    ['hour' => '06'],
                    ['hour' => '07'],
                    ['hour' => '08'],
                    ['hour' => '09'],
                    ['hour' => '10'],
                    ['hour' => '11'],
                    ['hour' => '12'],
                    ['hour' => '13'],
                    ['hour' => '14'],
                    ['hour' => '15'],
                    ['hour' => '16'],
                    ['hour' => '17'],
                    ['hour' => '18'],
                    ['hour' => '19'],
                    ['hour' => '20'],
                    ['hour' => '21'],
                    ['hour' => '22'],
                    ['hour' => '23'],
                    ['hour' => '24'],
                    ['hour' => '25'],
                    ['hour' => '26'],
                    ['hour' => '27'],
                    ['hour' => '28'],
                ],
                'aggregate' => [],
                'draw' => 1,
                'header' => ['header'],
            ])
            ->shouldBeCalled();

        $this->target->__invoke($output);
    }
}
